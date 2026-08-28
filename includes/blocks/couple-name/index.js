(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var RangeControl = components.RangeControl;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var useBlockProps = blockEditor.useBlockProps;
  var __ = i18n.__;

  var customColors = [
    { name: "Gold", color: "#b5a46d" },
    { name: "Dark Gold", color: "#8a7a4f" },
    { name: "White", color: "#ffffff" },
    { name: "Black", color: "#2c2c2c" },
    { name: "Maroon", color: "#800000" },
    { name: "Rose Gold", color: "#b76e79" },
  ];

  var coupleNameBuiltins = {
    playfair: "'Playfair Display', Georgia, serif",
    greatvibes: "'Great Vibes', cursive",
    montserrat: "'Montserrat', sans-serif",
    georgia: "Georgia, 'Times New Roman', serif",
    system: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
    "sans-serif": "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
    monospace: "'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace",
  };

  var coupleNameFontLabels = {
    playfair: __("Playfair Display (Serif Elegan)", "weddingblocks"),
    greatvibes: __("Great Vibes (Kaligrafi)", "weddingblocks"),
    montserrat: __("Montserrat (Sans-serif Modern)", "weddingblocks"),
    georgia: __("Georgia (Serif Klasik)", "weddingblocks"),
    system: __("System (Bawaan WordPress)", "weddingblocks"),
    "sans-serif": __("Sans-serif", "weddingblocks"),
    monospace: __("Monospace", "weddingblocks"),
  };

  // Font yang terdaftar dari tema (theme.json) dan Font Library WordPress.
  function mergeCoupleNameWpFonts(fonts) {
    var out = [];
    if (!Array.isArray(fonts)) {
      return out;
    }
    var builtins = [];
    Object.keys(coupleNameBuiltins).forEach(function (key) {
      builtins.push(coupleNameBuiltins[key].toLowerCase().replace(/\s+/g, " "));
    });
    for (var i = 0; i < fonts.length; i++) {
      var f = fonts[i];
      if (!f || !f.name || !f.fontFamily) {
        continue;
      }
      var normalized = String(f.fontFamily).toLowerCase().replace(/\s+/g, " ");
      if (builtins.indexOf(normalized) !== -1) {
        continue;
      }
      out.push({ label: String(f.name), value: String(f.fontFamily) });
    }
    return out;
  }

  function sanitizeCoupleNameFontCss(value) {
    var cleaned = String(value || "")
      .replace(/[^\w\s,()'"\-.]/g, "")
      .replace(/\s+/g, " ")
      .trim();
    if (!cleaned || cleaned.length > 300) {
      return "";
    }
    return cleaned;
  }

  function resolveCoupleNameFont(value) {
    if (!value || value === "default") {
      return "";
    }
    if (coupleNameBuiltins[value]) {
      return coupleNameBuiltins[value];
    }
    return sanitizeCoupleNameFontCss(value);
  }

  // Ekstrak font families dari semua origin (__experimentalFeatures / Font Library)
  function collectEditorFontFamilies(features) {
    var out = [];
    if (
      !features ||
      !features.typography ||
      !features.typography.fontFamilies
    ) {
      return out;
    }
    var groups = features.typography.fontFamilies;
    if (Array.isArray(groups)) {
      return groups.slice();
    }
    Object.keys(groups).forEach(function (origin) {
      var list = groups[origin];
      if (Array.isArray(list)) {
        out = out.concat(list);
      }
    });
    return out;
  }

  blocks.registerBlockType("weddingblocks/couple-name", {
    edit: function (props) {
      var attributes = props.attributes;
      var meta = wp.data.useSelect(function (select) {
        var editor = select("core/editor");
        if (!editor || typeof editor.getEditedPostAttribute !== "function") return {};
        return editor.getEditedPostAttribute("meta") || {};
      });

      // Baca daftar font dari block-editor __experimentalFeatures (Theme + WP Font Library)
      var wpFontFamilies = wp.data.useSelect(function (select) {
        var settings = select("core/block-editor").getSettings();
        return collectEditorFontFamilies(
          settings && settings.__experimentalFeatures
            ? settings.__experimentalFeatures
            : null
        );
      }, []);

      var role = attributes.role || "groom";
      var nameType = attributes.nameType || "full";
      var align = attributes.align || "center";
      var fontSize = attributes.fontSize || 32;
      var fontFamily = attributes.fontFamily || "default";
      var textColor = attributes.textColor || "";
      var textTransform = attributes.textTransform || "none";

      var groomName = attributes.groomName || meta.weddingblocks_groom_name || "";
      var groomNick = attributes.groomNickname || meta.weddingblocks_groom_nickname || "";
      var brideName = attributes.brideName || meta.weddingblocks_bride_name || "";
      var brideNick = attributes.brideNickname || meta.weddingblocks_bride_nickname || "";
      var fullName, nickName, fallback, roleLabel;

      if (role === "bride") {
        fullName = brideName; nickName = brideNick;
        fallback = __("Mempelai Wanita", "weddingblocks");
        roleLabel = __("Wanita", "weddingblocks");
      } else {
        fullName = groomName; nickName = groomNick;
        fallback = __("Mempelai Pria", "weddingblocks");
        roleLabel = __("Pria", "weddingblocks");
      }

      var display = nameType === "nickname"
        ? (nickName || fullName || fallback)
        : (fullName || nickName || fallback);

      var currentFontStack = resolveCoupleNameFont(fontFamily);

      // React inline style { fontFamily: ... } tidak mendukung !important,
      // terapkan secara imperatif lewat ref + setProperty("important") agar menang
      // atas stylesheet tema di editor.
      var fontRef = element.createRef();
      var useEffect = element.useEffect;
      if (typeof useEffect === "function") {
        useEffect(
          function () {
            if (fontRef && fontRef.current) {
              if (currentFontStack) {
                fontRef.current.style.setProperty(
                  "font-family",
                  currentFontStack,
                  "important"
                );
              } else {
                fontRef.current.style.removeProperty("font-family");
              }
            }
          },
          [currentFontStack]
        );
      }

      var previewStyle = { fontSize: fontSize + "px", textTransform: textTransform };
      if (textColor) { previewStyle.color = textColor; }

      var animPanel = typeof window.weddingblocksAnimationPanel === "function"
        ? window.weddingblocksAnimationPanel(attributes, props.setAttributes)
        : null;

      var fontOptions = (function () {
        var options = [
          { label: __("Bawaan Tema (Otomatis)", "weddingblocks"), value: "default" },
        ];
        Object.keys(coupleNameBuiltins).forEach(function (key) {
          options.push({
            label: coupleNameFontLabels[key] || key,
            value: key,
          });
        });
        mergeCoupleNameWpFonts(wpFontFamilies).forEach(function (font) {
          options.push({ label: font.label, value: font.value });
        });
        return options;
      })();

      return [
        el(InspectorControls, { key: "inspector" },
          el(PanelBody, { title: __("Pengaturan Nama Mempelai", "weddingblocks"), initialOpen: true },
            el(SelectControl, { label: __("Mempelai", "weddingblocks"), value: role, options: [{ label: __("Mempelai Pria", "weddingblocks"), value: "groom" }, { label: __("Mempelai Wanita", "weddingblocks"), value: "bride" }], onChange: function (v) { props.setAttributes({ role: v }); } }),
            el(SelectControl, { label: __("Perataan", "weddingblocks"), value: align, options: [{ label: __("Kiri", "weddingblocks"), value: "left" }, { label: __("Tengah", "weddingblocks"), value: "center" }, { label: __("Kanan", "weddingblocks"), value: "right" }], onChange: function (v) { props.setAttributes({ align: v }); } }),
            el(SelectControl, { label: __("Tipe Nama", "weddingblocks"), value: nameType, options: [{ label: __("Nama Lengkap", "weddingblocks"), value: "full" }, { label: __("Nama Panggilan", "weddingblocks"), value: "nickname" }], onChange: function (v) { props.setAttributes({ nameType: v }); } }),
            el(RangeControl, { label: __("Ukuran Font (px)", "weddingblocks"), value: fontSize, min: 12, max: 72, onChange: function (v) { props.setAttributes({ fontSize: v }); } }),
            el(SelectControl, {
              label: __("Jenis Font", "weddingblocks"),
              value: fontFamily,
              options: fontOptions,
              onChange: function (v) { props.setAttributes({ fontFamily: v }); },
              help: __("Pilih jenis huruf untuk nama mempelai. Font tema dan font dari Font Library WordPress otomatis tersedia.", "weddingblocks")
            }),
            el(SelectControl, { label: __("Transformasi Teks", "weddingblocks"), value: textTransform, options: [{ label: __("Normal", "weddingblocks"), value: "none" }, { label: __("HURUF BESAR (UPPERCASE)", "weddingblocks"), value: "uppercase" }, { label: __("Huruf Besar Di Awal (Capitalize)", "weddingblocks"), value: "capitalize" }, { label: __("huruf kecil (lowercase)", "weddingblocks"), value: "lowercase" }], onChange: function (v) { props.setAttributes({ textTransform: v }); } })
          ),
          el(PanelColorSettings, { title: __("Pengaturan Warna", "weddingblocks"), initialOpen: false, colorSettings: [{ value: textColor, colors: customColors, label: __("Warna Nama", "weddingblocks"), onChange: function (v) { props.setAttributes({ textColor: v || "" }); } }] })
        ),
        animPanel,
        el("div", useBlockProps({ key: "preview", className: "weddingblocks-atomic-couple-name role-" + role + " type-" + nameType + " align-" + align }),
          el("span", { className: "wb-editor-badge" }, el("span", { className: "wb-editor-badge-icon" }, "\uD83C\uDD94"), __("Nama " + roleLabel, "weddingblocks")),
          el("span", { className: "atomic-name-text", style: previewStyle, ref: fontRef }, display)
        ),
      ];
    },
    save: function () { return null; },
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

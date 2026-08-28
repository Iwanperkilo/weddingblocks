(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var Fragment = element.Fragment;
  var InspectorControls = blockEditor.InspectorControls;
  var BlockControls = blockEditor.BlockControls;
  var AlignmentToolbar = blockEditor.AlignmentToolbar;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var TextControl = components.TextControl;
  var ToggleControl = components.ToggleControl;
  var FontSizePicker = components.FontSizePicker;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var useBlockProps = blockEditor.useBlockProps;
  var __ = i18n.__;

  var colorPalette = [
    { name: __("White", "weddingblocks"), color: "#ffffff" },
    { name: __("Gold", "weddingblocks"), color: "#b5a46d" },
    { name: __("Dark Gold", "weddingblocks"), color: "#8a7a4f" },
    { name: __("Rose", "weddingblocks"), color: "#b76e79" },
    { name: __("Maroon", "weddingblocks"), color: "#800000" },
    { name: __("Charcoal", "weddingblocks"), color: "#2c2c2c" },
  ];

  // Preset font size, dipakai oleh FontSizePicker dan otomatis
  // konsisten dengan supports.typography.fontSize di block.json.
  var fontSizePresets = [
    { name: __("Kecil", "weddingblocks"), slug: "small", size: "24px" },
    { name: __("Sedang", "weddingblocks"), slug: "medium", size: "36px" },
    { name: __("Besar", "weddingblocks"), slug: "large", size: "48px" },
    { name: __("Sangat Besar", "weddingblocks"), slug: "x-large", size: "64px" },
  ];

  // Font bawaan (tanpa webfont tambahan — semuanya sudah dibundel plugin
  // atau memakai stack sistem WordPress). Kunci -> CSS font-family.
  var coupleTitleFonts = {
    playfair: "'Playfair Display', Georgia, serif",
    greatvibes: "'Great Vibes', cursive",
    montserrat: "'Montserrat', sans-serif",
    georgia: "Georgia, 'Times New Roman', serif",
    system: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
    "sans-serif": "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
    monospace: "'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace",
  };

  var coupleTitleFontLabels = {
    playfair: __("Playfair Display (Serif Elegan)", "weddingblocks"),
    greatvibes: __("Great Vibes (Kaligrafi)", "weddingblocks"),
    montserrat: __("Montserrat (Sans-serif Modern)", "weddingblocks"),
    georgia: __("Georgia (Serif Klasik)", "weddingblocks"),
    system: __("System (Bawaan WordPress)", "weddingblocks"),
    "sans-serif": __("Sans-serif", "weddingblocks"),
    monospace: __("Monospace", "weddingblocks"),
  };

  // Font yang terdaftar dari tema (theme.json) dan Font Library WordPress.
  // Nilainya adalah CSS font-family mentah (mis. var(--wp--preset--font-family--x)).
  function mergeCoupleTitleWpFonts(fonts) {
    var out = [];
    if (!Array.isArray(fonts)) {
      return out;
    }
    var builtins = [];
    Object.keys(coupleTitleFonts).forEach(function (key) {
      builtins.push(coupleTitleFonts[key].toLowerCase().replace(/\s+/g, " "));
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

  // Bersihkan nilai CSS font-family agar aman untuk output (tanpa `;`, `{`, `}`, HTML).
  function sanitizeCoupleTitleFontCss(value) {
    var cleaned = String(value || "")
      .replace(/[^\w\s,()'"\-.]/g, "")
      .replace(/\s+/g, " ")
      .trim();
    if (!cleaned || cleaned.length > 300) {
      return "";
    }
    return cleaned;
  }

  // Resolve nilai atribut font -> CSS font-family ("" bila default/bawaan).
  function resolveCoupleTitleFont(value) {
    if (!value || value === "default") {
      return "";
    }
    if (coupleTitleFonts[value]) {
      return coupleTitleFonts[value];
    }
    return sanitizeCoupleTitleFontCss(value);
  }

  var separatorPresets = [
    { label: "&", value: "&" },
    { label: "\u2764\ufe0f", value: "\u2764\ufe0f" },
    { label: __("Custom...", "weddingblocks"), value: "__custom__" },
  ];

  var HEART_PRESETS = ["\u2764\ufe0f", "\u2764"];

  function isHeartSeparator(value) {
    return HEART_PRESETS.indexOf(value) !== -1;
  }

  function renderHeartIcon(el) {
    return el(
      "svg",
      {
        className: "weddingblocks-heart-icon",
        viewBox: "0 0 32 29",
        width: "1em",
        height: "1em",
        "aria-hidden": "true",
        focusable: "false",
        xmlns: "http://www.w3.org/2000/svg",
      },
      el("path", {
        fill: "#E0303F",
        d: "M23.6 0c-3 0-5.8 1.5-7.6 3.9C14.2 1.5 11.4 0 8.4 0 3.8 0 0 3.7 0 8.4c0 8.5 10.5 14.6 15.1 18.8.5.5 1.3.5 1.8 0C21.5 23 32 16.9 32 8.4 32 3.7 28.2 0 23.6 0z",
      }),
    );
  }

  blocks.registerBlockType("weddingblocks/couple-title", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      var meta = wp.data.useSelect(function (select) {
        var editor = select("core/editor");
        if (!editor || typeof editor.getEditedPostAttribute !== "function") {
          return {};
        }
        return editor.getEditedPostAttribute("meta") || {};
      }, []);

      // Gabungkan font dari semua origin (theme, custom/Font Library, core)
      // yang terdapat di __experimentalFeatures — bukan typography.fontFamilies
      // yang kosong di pengaturan editor.
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

      var wpFontFamilies = wp.data.useSelect(function (select) {
        var settings = select("core/block-editor").getSettings();
        return collectEditorFontFamilies(
          settings && settings.__experimentalFeatures
            ? settings.__experimentalFeatures
            : null,
        );
      }, []);

      var currentFont =
        attributes.fontFamily ||
        attributes.wbproFontFamily ||
        "";

      var groomName =
        attributes.groomName || meta.weddingblocks_groom_name || "";
      var groomNickname =
        attributes.groomNickname || meta.weddingblocks_groom_nickname || "";
      var brideName =
        attributes.brideName || meta.weddingblocks_bride_name || "";
      var brideNickname =
        attributes.brideNickname || meta.weddingblocks_bride_nickname || "";

      var groomDisplay =
        groomNickname || groomName || __("Mempelai Pria", "weddingblocks");
      var brideDisplay =
        brideNickname || brideName || __("Mempelai Wanita", "weddingblocks");

      var textColor = attributes.textColor || "#ffffff";
      var textTransform = attributes.textTransform || "none";
      var separator =
        typeof attributes.separator === "string" ? attributes.separator : "&";
      var textAlign = attributes.textAlign || "center";
      var textShadow = !!attributes.textShadow;

      var isCustomSeparator = !separatorPresets.some(function (item) {
        return item.value === separator;
      });

      var transformText = function (text) {
        if (textTransform === "uppercase") {
          return text.toUpperCase();
        }
        if (textTransform === "lowercase") {
          return text.toLowerCase();
        }
        if (textTransform === "capitalize") {
          return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
        }
        return text;
      };

      var titleStyle = {
        color: textColor,
        textAlign: textAlign,
      };
      if (textShadow) {
        titleStyle.textShadow = "0 2px 6px rgba(0, 0, 0, 0.45)";
      }
      if (attributes.style && attributes.style.typography && attributes.style.typography.fontSize) {
        titleStyle.fontSize = attributes.style.typography.fontSize;
      }

      // Font Nama Cover: React tidak mendukung `!important` pada inline style,
// jadi preview diterapkan imperatif lewat ref + setProperty("important")
// agar menang atas `.weddingblocks-cover-title` {font-family: Playfair !important}.
      var currentFontStack = resolveCoupleTitleFont(currentFont);

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
                  "important",
                );
              } else {
                fontRef.current.style.removeProperty("font-family");
              }
            }
          },
          [currentFontStack],
        );
      }

      var animPanel = typeof window.weddingblocksAnimationPanel === "function"
        ? window.weddingblocksAnimationPanel(attributes, setAttributes)
        : null;

      return [
        el(
          BlockControls,
          { key: "toolbar" },
          el(AlignmentToolbar, {
            value: textAlign,
            onChange: function (value) {
              setAttributes({ textAlign: value || "center" });
            },
          }),
        ),
        el(
          InspectorControls,
          { key: "inspector" },
          el(PanelColorSettings, {
            title: __("Warna", "weddingblocks"),
            initialOpen: true,
            colorSettings: [
              {
                value: textColor,
                colors: colorPalette,
                label: __("Warna Teks", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ textColor: value || "#ffffff" });
                },
              },
            ],
          }),
          el(
            PanelBody,
            {
              title: __("Tipografi", "weddingblocks"),
              initialOpen: true,
            },
            el(FontSizePicker, {
              value:
                attributes.style &&
                attributes.style.typography &&
                attributes.style.typography.fontSize,
              fontSizes: fontSizePresets,
              onChange: function (value) {
                setAttributes({
                  style: Object.assign({}, attributes.style, {
                    typography: Object.assign(
                      {},
                      attributes.style && attributes.style.typography,
                      { fontSize: value },
                    ),
                  }),
                });
              },
            }),
            el(SelectControl, {
              label: __("Jenis Font Nama Cover", "weddingblocks"),
              value: currentFont,
              options: (function () {
                var options = [
                  { label: __("Bawaan Tema (Otomatis)", "weddingblocks"), value: "" },
                ];
                Object.keys(coupleTitleFonts).forEach(function (key) {
                  options.push({
                    label: coupleTitleFontLabels[key] || key,
                    value: key,
                  });
                });
                mergeCoupleTitleWpFonts(wpFontFamilies).forEach(
                  function (font) {
                    options.push({ label: font.label, value: font.value });
                  },
                );
                return options;
              })(),
              onChange: function (value) {
                setAttributes({ fontFamily: value });
              },
              help: __(
                "Pilih jenis huruf untuk judul Nama Cover. Font tema dan font dari Font Library WordPress otomatis tersedia.",
                "weddingblocks",
              ),
            }),
            el(
              "div",
              { style: { marginTop: "20px" } },
              el(ToggleControl, {
                label: __("Efek Bayangan Teks", "weddingblocks"),
                checked: textShadow,
                onChange: function (value) {
                  setAttributes({ textShadow: value });
                },
              }),
            ),
          ),
          el(
            PanelBody,
            {
              title: __("Pengaturan Nama Cover", "weddingblocks"),
              initialOpen: true,
            },
            el(SelectControl, {
              label: __("Transformasi Teks", "weddingblocks"),
              value: textTransform,
              options: [
                { label: __("None", "weddingblocks"), value: "none" },
                { label: __("Uppercase", "weddingblocks"), value: "uppercase" },
                { label: __("Lowercase", "weddingblocks"), value: "lowercase" },
                {
                  label: __("Capitalize", "weddingblocks"),
                  value: "capitalize",
                },
              ],
              onChange: function (value) {
                setAttributes({ textTransform: value });
              },
            }),
            el(SelectControl, {
              label: __("Tanda Hubung", "weddingblocks"),
              value: isCustomSeparator ? "__custom__" : separator,
              options: separatorPresets,
              onChange: function (value) {
                if (value === "__custom__") {
                  setAttributes({ separator: "" });
                } else {
                  setAttributes({ separator: value });
                }
              },
            }),
            isCustomSeparator &&
            el(TextControl, {
              label: __("Tanda Hubung Custom", "weddingblocks"),
              value: separator,
              placeholder: __("mis. dan", "weddingblocks"),
              onChange: function (value) {
                setAttributes({ separator: value });
              },
            }),
          ),
        ),
        animPanel,
        el(
          "div",
          useBlockProps({ key: "preview" }),
          el(
            "span",
            { className: "wb-editor-badge wb-editor-badge--block" },
            el("span", { className: "wb-editor-badge-icon" }, "\ud83d\udc51"),
            __("Nama Pengantin", "weddingblocks"),
          ),
          el(
            "h2",
            {
              className: "weddingblocks-couple-title-text weddingblocks-cover-title",
              style: titleStyle,
              ref: fontRef,
            },
            transformText(groomDisplay) + " ",
            el(
              "span",
              {
                className:
                  "weddingblocks-separator" +
                  (isHeartSeparator(separator) ? " weddingblocks-separator--icon" : ""),
              },
              isHeartSeparator(separator) ? renderHeartIcon(el) : separator,
            ),
            " " + transformText(brideDisplay),
          ),
        ),
      ];
    },
    save: function () {
      return null;
    },
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n,
);
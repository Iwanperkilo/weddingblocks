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

  blocks.registerBlockType("weddingblocks/couple-name", {
    edit: function (props) {
      var attributes = props.attributes;
      var meta = wp.data.useSelect(function (select) {
        return select("core/editor").getEditedPostAttribute("meta") || {};
      });

      var role = attributes.role || "groom";
      var nameType = attributes.nameType || "full";
      var align = attributes.align || "center";
      var fontSize = attributes.fontSize || 32;
      var fontFamily = attributes.fontFamily || "playfair";
      var textColor = attributes.textColor || "#b5a46d";
      var textTransform = attributes.textTransform || "none";

      var groomName =
        attributes.groomName || meta.weddingblocks_groom_name || "";
      var groomNick =
        attributes.groomNickname || meta.weddingblocks_groom_nickname || "";
      var brideName =
        attributes.brideName || meta.weddingblocks_bride_name || "";
      var brideNick =
        attributes.brideNickname || meta.weddingblocks_bride_nickname || "";
      var fullName, nickName, fallback, roleLabel;

      if (role === "bride") {
        fullName = brideName;
        nickName = brideNick;
        fallback = __("Mempelai Wanita", "weddingblocks");
        roleLabel = __("Wanita", "weddingblocks");
      } else {
        fullName = groomName;
        nickName = groomNick;
        fallback = __("Mempelai Pria", "weddingblocks");
        roleLabel = __("Pria", "weddingblocks");
      }

      var display;
      if (nameType === "nickname") {
        display = nickName || fullName || fallback;
      } else if (nameType === "both") {
        display = (nickName + " " + fullName).trim() || fallback;
      } else {
        display = fullName || nickName || fallback;
      }

      var fontFamilyCSS = "serif";
      if (fontFamily === "playfair") {
        fontFamilyCSS = "'Playfair Display', Georgia, serif";
      } else if (fontFamily === "greatvibes") {
        fontFamilyCSS = "'Great Vibes', cursive";
      } else if (fontFamily === "montserrat") {
        fontFamilyCSS = "'Montserrat', sans-serif";
      } else if (fontFamily === "georgia") {
        fontFamilyCSS = "Georgia, serif";
      } else if (fontFamily === "sans-serif") {
        fontFamilyCSS =
          "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
      }

      var previewStyle = {
        fontSize: fontSize + "px",
        fontFamily: fontFamilyCSS,
        color: textColor,
        textTransform: textTransform,
      };

      return [
        el(
          InspectorControls,
          { key: "inspector" },
          el(
            PanelBody,
            {
              title: __("Pengaturan Nama Mempelai", "weddingblocks"),
              initialOpen: true,
            },
            el(SelectControl, {
              label: __("Mempelai", "weddingblocks"),
              value: role,
              options: [
                { label: __("Mempelai Pria", "weddingblocks"), value: "groom" },
                {
                  label: __("Mempelai Wanita", "weddingblocks"),
                  value: "bride",
                },
              ],
              onChange: function (v) {
                props.setAttributes({ role: v });
              },
            }),
            el(SelectControl, {
              label: __("Perataan", "weddingblocks"),
              value: align,
              options: [
                { label: __("Kiri", "weddingblocks"), value: "left" },
                { label: __("Tengah", "weddingblocks"), value: "center" },
                { label: __("Kanan", "weddingblocks"), value: "right" },
              ],
              onChange: function (v) {
                props.setAttributes({ align: v });
              },
            }),
            el(SelectControl, {
              label: __("Tipe Nama", "weddingblocks"),
              value: nameType,
              options: [
                { label: __("Nama Lengkap", "weddingblocks"), value: "full" },
                {
                  label: __("Nama Panggilan", "weddingblocks"),
                  value: "nickname",
                },
                { label: __("Keduanya", "weddingblocks"), value: "both" },
              ],
              onChange: function (v) {
                props.setAttributes({ nameType: v });
              },
            }),
            el(RangeControl, {
              label: __("Ukuran Font (px)", "weddingblocks"),
              value: fontSize,
              min: 12,
              max: 72,
              onChange: function (v) {
                props.setAttributes({ fontSize: v });
              },
            }),
            el(SelectControl, {
              label: __("Jenis Font", "weddingblocks"),
              value: fontFamily,
              options: [
                {
                  label: __(
                    "Playfair Display (Serif Elegant)",
                    "weddingblocks",
                  ),
                  value: "playfair",
                },
                {
                  label: __("Great Vibes (Calligraphy)", "weddingblocks"),
                  value: "greatvibes",
                },
                {
                  label: __("Montserrat (Modern Sans-serif)", "weddingblocks"),
                  value: "montserrat",
                },
                {
                  label: __("Georgia (Classic Serif)", "weddingblocks"),
                  value: "georgia",
                },
                {
                  label: __("System Sans-serif", "weddingblocks"),
                  value: "sans-serif",
                },
              ],
              onChange: function (v) {
                props.setAttributes({ fontFamily: v });
              },
            }),
            el(SelectControl, {
              label: __("Transformasi Teks", "weddingblocks"),
              value: textTransform,
              options: [
                { label: __("Normal", "weddingblocks"), value: "none" },
                {
                  label: __("HURUF BESAR (UPPERCASE)", "weddingblocks"),
                  value: "uppercase",
                },
                {
                  label: __(
                    "Huruf Besar Di Awal (Capitalize)",
                    "weddingblocks",
                  ),
                  value: "capitalize",
                },
                {
                  label: __("huruf kecil (lowercase)", "weddingblocks"),
                  value: "lowercase",
                },
              ],
              onChange: function (v) {
                props.setAttributes({ textTransform: v });
              },
            }),
          ),
          el(PanelColorSettings, {
            title: __("Pengaturan Warna", "weddingblocks"),
            initialOpen: false,
            colorSettings: [
              {
                value: textColor,
                colors: customColors,
                label: __("Warna Nama", "weddingblocks"),
                onChange: function (v) {
                  props.setAttributes({ textColor: v || "#b5a46d" });
                },
              },
            ],
          }),
        ),
        el(
          "div",
          useBlockProps({
            key: "preview",
            className:
              "weddingblocks-atomic-couple-name role-" +
              role +
              " type-" +
              nameType +
              " align-" +
              align,
          }),
          el(
            "span",
            { className: "wb-editor-badge" },
            el("span", { className: "wb-editor-badge-icon" }, "🆔"),
            __("Nama " + roleLabel, "weddingblocks"),
          ),
          el(
            "span",
            { className: "atomic-name-text", style: previewStyle },
            display,
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

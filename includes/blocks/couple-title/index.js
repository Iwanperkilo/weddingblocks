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

  var separatorPresets = [
    { label: "&", value: "&" },
    { label: "\u2764\ufe0f", value: "\u2764\ufe0f" },
    { label: __("Custom...", "weddingblocks"), value: "__custom__" },
  ];

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
        textTransform: textTransform,
        textAlign: textAlign,
      };
      if (textShadow) {
        titleStyle.textShadow = "0 2px 6px rgba(0, 0, 0, 0.45)";
      }
      if (attributes.style && attributes.style.typography && attributes.style.typography.fontSize) {
        titleStyle.fontSize = attributes.style.typography.fontSize;
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
              className: "weddingblocks-couple-title-text",
              style: titleStyle,
            },
            transformText(groomDisplay) +
            " " +
            separator +
            " " +
            transformText(brideDisplay),
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

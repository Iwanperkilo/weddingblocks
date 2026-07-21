(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var RangeControl = components.RangeControl;
  var SelectControl = components.SelectControl;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var MediaUpload = blockEditor.MediaUpload;
  var Button = components.Button;
  var InnerBlocks = blockEditor.InnerBlocks;
  var useBlockProps = blockEditor.useBlockProps;
  var __ = i18n.__;

  // Helper to determine contrast color based on luminance
  function getContrastColor(hexColor) {
    if (!hexColor) return "#ffffff";
    var hex = hexColor.replace("#", "");
    if (hex.length === 3) {
      hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
      return "#ffffff";
    }
    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);
    var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.6 ? "#1c1d1d" : "#ffffff";
  }

  blocks.registerBlockType("weddingblocks/cover", {
    edit: function (props) {
      var attributes = props.attributes;
      var buttonBorderRadius =
        attributes.buttonBorderRadius !== undefined
          ? attributes.buttonBorderRadius
          : 30;
      var coverWidth = attributes.coverWidth || "full";
      var coverCustomWidth =
        attributes.coverCustomWidth !== undefined
          ? attributes.coverCustomWidth
          : 480;

      var customColors = [
        { name: "Gold", color: "#d4c59a" },
        { name: "Dark Gold", color: "#b5a46d" },
        { name: "White", color: "#ffffff" },
        { name: "Black", color: "#000000" },
        { name: "Maroon", color: "#800000" },
        { name: "Navy", color: "#000080" },
      ];

      // Default template for inner blocks
      var COVER_TEMPLATE = [
        [
          "core/paragraph",
          {
            placeholder: "WALIMATUL 'URSY",
            className: "weddingblocks-cover-welcome",
            align: "center",
          },
        ],
        [
          "core/heading",
          {
            placeholder: "Mempelai Pria & Mempelai Wanita",
            className: "weddingblocks-cover-title",
            textAlign: "center",
            level: 1,
          },
        ],
        [
          "weddingblocks/guest-name",
          {
            prefix: "Kepada Yth. Bapak/Ibu/Saudara/i:",
            fallback: "Tamu Undangan",
          },
        ],
      ];

      return [
        el(
          InspectorControls,
          { key: "inspector" },
          el(
            PanelBody,
            {
              title: __("Pengaturan Cover", "weddingblocks"),
              initialOpen: true,
            },
            el("p", {}, __("Gambar Latar Cover:", "weddingblocks")),
            el(MediaUpload, {
              onSelect: function (media) {
                props.setAttributes({ backgroundImage: media.url });
              },
              allowedTypes: ["image"],
              value: attributes.backgroundImage,
              render: function (obj) {
                return el(
                  "div",
                  {},
                  el(
                    Button,
                    {
                      isSecondary: true,
                      onClick: obj.open,
                      style: { marginBottom: "10px" },
                    },
                    !attributes.backgroundImage
                      ? __("Pilih Gambar", "weddingblocks")
                      : __("Ganti Gambar", "weddingblocks"),
                  ),
                  attributes.backgroundImage &&
                  el(
                    Button,
                    {
                      isDestructive: true,
                      onClick: function () {
                        props.setAttributes({ backgroundImage: "" });
                      },
                      style: { marginLeft: "10px", marginBottom: "10px" },
                    },
                    __("Hapus", "weddingblocks"),
                  ),
                );
              },
            }),
            el(TextControl, {
              label: __("Teks Tombol Pembuka", "weddingblocks"),
              value: attributes.buttonText,
              onChange: function (value) {
                props.setAttributes({ buttonText: value });
              },
            }),
            el(RangeControl, {
              label: __("Radius Sudut Tombol", "weddingblocks"),
              value: buttonBorderRadius,
              onChange: function (value) {
                props.setAttributes({ buttonBorderRadius: value });
              },
              min: 0,
              max: 50,
              step: 1,
            }),
            el(RangeControl, {
              label: __("Overlay (%)", "weddingblocks"),
              value:
                attributes.overlayOpacity !== undefined
                  ? attributes.overlayOpacity
                  : 35,
              onChange: function (value) {
                props.setAttributes({ overlayOpacity: value });
              },
              min: 0,
              max: 95,
              step: 5,
            }),
          ),
          el(
            PanelBody,
            {
              initialOpen: true,
            },
            el(SelectControl, {
              label: __("Mode Cover", "weddingblocks"),
              value: coverWidth,
              options: [
                { label: __("Full (layar penuh)", "weddingblocks"), value: "full" },
                { label: __("Mobile (± 480px)", "weddingblocks"), value: "mobile" },
                { label: __("Custom", "weddingblocks"), value: "custom" },
              ],
              onChange: function (value) {
                props.setAttributes({ coverWidth: value });
              },
              help:
                coverWidth === "full"
                  ? __("Cover memenuhi seluruh lebar layar.", "weddingblocks")
                  : __("Isi undangan lain di bawah cover tetap memakai lebar aslinya.", "weddingblocks"),
            }),
            coverWidth === "custom" &&
            el(RangeControl, {
              label: __("Lebar Custom (px)", "weddingblocks"),
              value: coverCustomWidth,
              onChange: function (value) {
                props.setAttributes({ coverCustomWidth: value });
              },
              min: 280,
              max: 960,
              step: 10,
            }),
          ),
          el(PanelColorSettings, {
            title: __("Warna", "weddingblocks"),
            initialOpen: false,
            colorSettings: [
              {
                value: attributes.overlayColor || "#000000",
                colors: customColors,
                label: __("Warna Background", "weddingblocks"),
                onChange: function (value) {
                  props.setAttributes({ overlayColor: value || "#000000" });
                },
              },
              {
                value: attributes.accentColor || "#b5a46d",
                colors: customColors,
                label: __("Warna Aksen & Tombol", "weddingblocks"),
                onChange: function (value) {
                  props.setAttributes({ accentColor: value || "#b5a46d" });
                },
              },
            ],
          }),
        ),
        el(
          "div",
          useBlockProps({
            key: "editor-preview",
            className: "weddingblocks-cover-preview",
            style: {
              position: "relative",
              padding: "60px 20px",
              border: "1px solid " + (attributes.accentColor || "#b5a46d"),
              borderRadius: "12px",
              margin: "10px auto",
              maxWidth:
                coverWidth === "mobile"
                  ? "480px"
                  : coverWidth === "custom"
                    ? coverCustomWidth + "px"
                    : "none",
              backgroundImage: attributes.backgroundImage
                ? "url(" + attributes.backgroundImage + ")"
                : "none",
              backgroundColor: attributes.backgroundImage
                ? "transparent"
                : attributes.overlayColor || "#1c1d1d",
              backgroundSize: "cover",
              backgroundPosition: "center",
              textAlign: "center",
              overflow: "hidden",
            },
          }),
          el(
            "span",
            {
              className: "wb-editor-badge wb-editor-badge--block",
              style: { zIndex: 3 },
            },
            el("span", { className: "wb-editor-badge-icon" }, "💌"),
            __("Cover Undangan", "weddingblocks"),
          ),
          // Dark Overlay
          el("div", {
            style: {
              position: "absolute",
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              backgroundColor: attributes.overlayColor || "#000000",
              opacity:
                (attributes.overlayOpacity !== undefined
                  ? attributes.overlayOpacity
                  : 35) / 100,
              zIndex: 1,
            },
          }),
          // Content Wrapper
          el(
            "div",
            {
              style: {
                position: "relative",
                zIndex: 2,
              },
            },
            el(InnerBlocks, {
              template: COVER_TEMPLATE,
              templateLock: false,
            }),
            // Button
            el(
              "div",
              { style: { marginTop: "20px" } },
              el(
                "span",
                {
                  className: "weddingblocks-btn-gold",
                  style: {
                    display: "inline-block",
                    padding: "12px 28px",
                    backgroundColor: attributes.accentColor || "#b5a46d",
                    color: getContrastColor(attributes.accentColor || "#b5a46d"),
                    borderRadius: buttonBorderRadius + "px",
                    fontSize: "14px",
                    fontWeight: "600",
                    letterSpacing: "1px",
                    textTransform: "uppercase",
                    cursor: "pointer",
                    boxShadow: "0 4px 15px rgba(181, 164, 109, 0.2)",
                  },
                },
                attributes.buttonText || "Buka Undangan",
              ),
            ),
          ),
        ),
      ];
    },
    save: function () {
      return el(InnerBlocks.Content);
    },
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n,
);
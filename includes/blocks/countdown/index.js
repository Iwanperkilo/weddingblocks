(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var __ = i18n.__;

  var colorPalette = [
    { name: __("White", "weddingblocks"), color: "#ffffff" },
    { name: __("Gold", "weddingblocks"), color: "#b5a46d" },
    { name: __("Dark Gold", "weddingblocks"), color: "#8a7a4f" },
    { name: __("Rose", "weddingblocks"), color: "#b76e79" },
    { name: __("Maroon", "weddingblocks"), color: "#800000" },
    { name: __("Charcoal", "weddingblocks"), color: "#2c2c2c" },
  ];

  blocks.registerBlockType("weddingblocks/countdown", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      var meta = wp.data.useSelect(function (select) {
        return select("core/editor").getEditedPostAttribute("meta") || {};
      });

      var weddingDate = meta.weddingblocks_wedding_date || "2026-12-31T09:00";

      var textColor = attributes.textColor || "#b5a46d";
      var labelColor = attributes.labelColor || "#888888";
      var boxBackgroundColor = attributes.boxBackgroundColor || "#ffffff";
      var borderColor = attributes.borderColor || "#b5a46d";

      return [
        el(
          InspectorControls,
          { key: "inspector" },
          el(PanelColorSettings, {
            title: __("Pengaturan Warna Countdown", "weddingblocks"),
            initialOpen: true,
            colorSettings: [
              {
                value: textColor,
                colors: colorPalette,
                label: __("Warna Angka", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ textColor: value || "#b5a46d" });
                },
              },
              {
                value: labelColor,
                colors: colorPalette,
                label: __("Warna Label", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ labelColor: value || "#888888" });
                },
              },
              {
                value: boxBackgroundColor,
                colors: colorPalette,
                label: __("Warna Background Box", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ boxBackgroundColor: value || "#ffffff" });
                },
              },
              {
                value: borderColor,
                colors: colorPalette,
                label: __("Warna Border", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ borderColor: value || "#b5a46d" });
                },
              },
            ],
          }),
        ),
        el(
          "div",
          useBlockProps({
            key: "editor-preview",
            className: "weddingblocks-countdown-editor",
          }),
          el(
            "span",
            { className: "wb-editor-badge wb-editor-badge--block" },
            el("span", { className: "wb-editor-badge-icon" }, "⏳"),
            __("Countdown", "weddingblocks"),
          ),
          el(
            "p",
            {
              style: {
                fontSize: "12px",
                color: "#888",
                textAlign: "center",
                margin: "0 0 8px",
              },
            },
            "📅 " + weddingDate,
          ),
          el(
            "div",
            { className: "weddingblocks-countdown" },
            el(
              "div",
              {
                className: "countdown-item",
                style: {
                  backgroundColor: boxBackgroundColor,
                  borderColor: borderColor,
                },
              },
              el(
                "span",
                { className: "countdown-value", style: { color: textColor } },
                "00",
              ),
              el(
                "span",
                { className: "countdown-label", style: { color: labelColor } },
                __("Hari", "weddingblocks"),
              ),
            ),
            el(
              "div",
              {
                className: "countdown-item",
                style: {
                  backgroundColor: boxBackgroundColor,
                  borderColor: borderColor,
                },
              },
              el(
                "span",
                { className: "countdown-value", style: { color: textColor } },
                "00",
              ),
              el(
                "span",
                { className: "countdown-label", style: { color: labelColor } },
                __("Jam", "weddingblocks"),
              ),
            ),
            el(
              "div",
              {
                className: "countdown-item",
                style: {
                  backgroundColor: boxBackgroundColor,
                  borderColor: borderColor,
                },
              },
              el(
                "span",
                { className: "countdown-value", style: { color: textColor } },
                "00",
              ),
              el(
                "span",
                { className: "countdown-label", style: { color: labelColor } },
                __("Menit", "weddingblocks"),
              ),
            ),
            el(
              "div",
              {
                className: "countdown-item",
                style: {
                  backgroundColor: boxBackgroundColor,
                  borderColor: borderColor,
                },
              },
              el(
                "span",
                { className: "countdown-value", style: { color: textColor } },
                "00",
              ),
              el(
                "span",
                { className: "countdown-label", style: { color: labelColor } },
                __("Detik", "weddingblocks"),
              ),
            ),
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

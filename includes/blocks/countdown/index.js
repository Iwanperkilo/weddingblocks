(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var PanelBody = components.PanelBody;
  var RangeControl = components.RangeControl;
  var ToggleControl = components.ToggleControl;
  var TextControl = components.TextControl;
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

      var borderWidth =
        typeof attributes.borderWidth === "number" ? attributes.borderWidth : 1;
      var borderRadius =
        typeof attributes.borderRadius === "number"
          ? attributes.borderRadius
          : 15;
      var boxShadow =
        typeof attributes.boxShadow === "boolean" ? attributes.boxShadow : true;
      var valueFontSize =
        typeof attributes.valueFontSize === "number"
          ? attributes.valueFontSize
          : 32;
      var labelFontSize =
        typeof attributes.labelFontSize === "number"
          ? attributes.labelFontSize
          : 10;
      var gap = typeof attributes.gap === "number" ? attributes.gap : 15;
      var boxPaddingVertical =
        typeof attributes.boxPaddingVertical === "number"
          ? attributes.boxPaddingVertical
          : 15;
      var boxPaddingHorizontal =
        typeof attributes.boxPaddingHorizontal === "number"
          ? attributes.boxPaddingHorizontal
          : 5;

      var labelDays = attributes.labelDays || __("Hari", "weddingblocks");
      var labelHours = attributes.labelHours || __("Jam", "weddingblocks");
      var labelMinutes =
        attributes.labelMinutes || __("Menit", "weddingblocks");
      var labelSeconds =
        attributes.labelSeconds || __("Detik", "weddingblocks");

      var itemStyle = {
        backgroundColor: boxBackgroundColor,
        borderColor: borderColor,
        borderWidth: borderWidth + "px",
        borderStyle: "solid",
        borderRadius: borderRadius + "px",
        boxShadow: boxShadow ? "3px 6px 8px rgb(145 145 145 / 54%)" : "none",
        padding: boxPaddingVertical + "px " + boxPaddingHorizontal + "px",
      };

      var makeItem = function (label) {
        return el(
          "div",
          { className: "countdown-item", style: itemStyle },
          el(
            "span",
            {
              className: "countdown-value",
              style: { color: textColor, fontSize: valueFontSize + "px" },
            },
            "00",
          ),
          el(
            "span",
            {
              className: "countdown-label",
              style: { color: labelColor, fontSize: labelFontSize + "px" },
            },
            label,
          ),
        );
      };

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
                enableAlpha: true,
                onChange: function (value) {
                  setAttributes({ textColor: value || "#b5a46d" });
                },
              },
              {
                value: labelColor,
                colors: colorPalette,
                label: __("Warna Label", "weddingblocks"),
                enableAlpha: true,
                onChange: function (value) {
                  setAttributes({ labelColor: value || "#888888" });
                },
              },
              {
                value: boxBackgroundColor,
                colors: colorPalette,
                label: __("Warna Background Box", "weddingblocks"),
                enableAlpha: true,
                onChange: function (value) {
                  setAttributes({ boxBackgroundColor: value || "#ffffff" });
                },
              },
              {
                value: borderColor,
                colors: colorPalette,
                label: __("Warna Border", "weddingblocks"),
                enableAlpha: true,
                onChange: function (value) {
                  setAttributes({ borderColor: value || "#b5a46d" });
                },
              },
            ],
          }),
          el(
            PanelBody,
            {
              title: __("Pengaturan Box & Tipografi", "weddingblocks"),
              initialOpen: false,
            },
            el(RangeControl, {
              label: __("Ketebalan Border (px)", "weddingblocks"),
              value: borderWidth,
              min: 0,
              max: 8,
              onChange: function (value) {
                setAttributes({ borderWidth: value === undefined ? 1 : value });
              },
            }),
            el(RangeControl, {
              label: __("Border Radius (px)", "weddingblocks"),
              value: borderRadius,
              min: 0,
              max: 50,
              onChange: function (value) {
                setAttributes({
                  borderRadius: value === undefined ? 15 : value,
                });
              },
            }),
            el(ToggleControl, {
              label: __("Aktifkan Box Shadow", "weddingblocks"),
              checked: boxShadow,
              onChange: function (value) {
                setAttributes({ boxShadow: value });
              },
            }),
            el(RangeControl, {
              label: __("Ukuran Font Angka (px)", "weddingblocks"),
              value: valueFontSize,
              min: 16,
              max: 60,
              onChange: function (value) {
                setAttributes({
                  valueFontSize: value === undefined ? 32 : value,
                });
              },
            }),
            el(RangeControl, {
              label: __("Ukuran Font Label (px)", "weddingblocks"),
              value: labelFontSize,
              min: 8,
              max: 20,
              onChange: function (value) {
                setAttributes({
                  labelFontSize: value === undefined ? 10 : value,
                });
              },
            }),
            el(RangeControl, {
              label: __("Jarak Antar Box (px)", "weddingblocks"),
              value: gap,
              min: 0,
              max: 40,
              onChange: function (value) {
                setAttributes({ gap: value === undefined ? 15 : value });
              },
            }),
            el(RangeControl, {
              label: __("Padding Atas-Bawah Box (px)", "weddingblocks"),
              value: boxPaddingVertical,
              min: 0,
              max: 40,
              onChange: function (value) {
                setAttributes({
                  boxPaddingVertical: value === undefined ? 15 : value,
                });
              },
            }),
            el(RangeControl, {
              label: __("Padding Kiri-Kanan Box (px)", "weddingblocks"),
              value: boxPaddingHorizontal,
              min: 0,
              max: 40,
              onChange: function (value) {
                setAttributes({
                  boxPaddingHorizontal: value === undefined ? 5 : value,
                });
              },
            }),
          ),
          el(
            PanelBody,
            {
              title: __("Teks Label", "weddingblocks"),
              initialOpen: false,
            },
            el(TextControl, {
              label: __("Label Hari", "weddingblocks"),
              value: labelDays,
              onChange: function (value) {
                setAttributes({ labelDays: value });
              },
            }),
            el(TextControl, {
              label: __("Label Jam", "weddingblocks"),
              value: labelHours,
              onChange: function (value) {
                setAttributes({ labelHours: value });
              },
            }),
            el(TextControl, {
              label: __("Label Menit", "weddingblocks"),
              value: labelMinutes,
              onChange: function (value) {
                setAttributes({ labelMinutes: value });
              },
            }),
            el(TextControl, {
              label: __("Label Detik", "weddingblocks"),
              value: labelSeconds,
              onChange: function (value) {
                setAttributes({ labelSeconds: value });
              },
            }),
          ),
        ),
        el(
          "div",
          useBlockProps({
            key: "editor-preview",
            className: "weddingblocks-countdown-editor",
          }),
          el(
            "div",
            {
              className: "weddingblocks-countdown",
              style: { gap: gap + "px" },
            },
            makeItem(labelDays),
            makeItem(labelHours),
            makeItem(labelMinutes),
            makeItem(labelSeconds),
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
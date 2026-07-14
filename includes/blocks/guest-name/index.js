(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var ToggleControl = components.ToggleControl;
  var ToggleGroupControl = components.__experimentalToggleGroupControl;
  var ToggleGroupControlOption =
    components.__experimentalToggleGroupControlOption;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var useBlockProps = blockEditor.useBlockProps;
  var __ = i18n.__;

  var FONT_SIZE_MAP = {
    small: "15px",
    medium: "18px",
    large: "24px",
  };

  blocks.registerBlockType("weddingblocks/guest-name", {
    edit: function (props) {
      var attributes = props.attributes;
      var nameFontSize =
        FONT_SIZE_MAP[attributes.fontSize] || FONT_SIZE_MAP.medium;

      return [
        el(
          InspectorControls,
          { key: "inspector" },
          el(
            PanelBody,
            {
              title: __("Pengaturan Nama Tamu", "weddingblocks"),
              initialOpen: true,
            },
            el(TextControl, {
              label: __("Teks Pembuka (Prefix)", "weddingblocks"),
              value: attributes.prefix,
              onChange: function (value) {
                props.setAttributes({ prefix: value });
              },
            }),
            el(ToggleControl, {
              label: __(
                "Tampilkan Teks Pembuka (Prefix)",
                "weddingblocks",
              ),
              checked: attributes.showPrefix,
              onChange: function (value) {
                props.setAttributes({ showPrefix: value });
              },
            }),
            el(TextControl, {
              label: __("Teks Cadangan (Fallback)", "weddingblocks"),
              value: attributes.fallback,
              onChange: function (value) {
                props.setAttributes({ fallback: value });
              },
              help: __(
                "Ditampilkan jika link undangan diakses tanpa parameter ?to=NamaTamu.",
                "weddingblocks",
              ),
            }),
            el(
              ToggleGroupControl,
              {
                label: __("Perataan Teks", "weddingblocks"),
                value: attributes.textAlign,
                isBlock: true,
                onChange: function (value) {
                  props.setAttributes({ textAlign: value });
                },
                __nextHasNoMarginBottom: true,
              },
              el(ToggleGroupControlOption, {
                value: "left",
                label: __("Kiri", "weddingblocks"),
              }),
              el(ToggleGroupControlOption, {
                value: "center",
                label: __("Tengah", "weddingblocks"),
              }),
              el(ToggleGroupControlOption, {
                value: "right",
                label: __("Kanan", "weddingblocks"),
              }),
            ),
            el(
              ToggleGroupControl,
              {
                label: __("Ukuran Nama Tamu", "weddingblocks"),
                value: attributes.fontSize,
                isBlock: true,
                onChange: function (value) {
                  props.setAttributes({ fontSize: value });
                },
                __nextHasNoMarginBottom: true,
              },
              el(ToggleGroupControlOption, {
                value: "small",
                label: __("Kecil", "weddingblocks"),
              }),
              el(ToggleGroupControlOption, {
                value: "medium",
                label: __("Sedang", "weddingblocks"),
              }),
              el(ToggleGroupControlOption, {
                value: "large",
                label: __("Besar", "weddingblocks"),
              }),
            ),
          ),
          el(PanelColorSettings, {
            title: __("Pengaturan Warna", "weddingblocks"),
            initialOpen: false,
            colorSettings: [
              {
                value: attributes.textColor,
                colors: [
                  { name: "Black", slug: "black", color: "#000000" },
                  { name: "Dark Gray", slug: "dark-gray", color: "#333333" },
                  {
                    name: "Medium Gray",
                    slug: "medium-gray",
                    color: "#777777",
                  },
                  { name: "Light Gray", slug: "light-gray", color: "#eeeeee" },
                  { name: "White", slug: "white", color: "#ffffff" },
                  { name: "Gold", slug: "gold", color: "#b5a46d" },
                ],
                label: __("Warna Teks", "weddingblocks"),
                enableAlpha: true,
                onChange: function (value) {
                  props.setAttributes({ textColor: value });
                },
              },
              {
                value: attributes.backgroundColor,
                colors: [
                  {
                    name: "Transparent",
                    slug: "transparent",
                    color: "transparent",
                  },
                  { name: "White", slug: "white", color: "#ffffff" },
                  { name: "Light Gray", slug: "light-gray", color: "#eeeeee" },
                  { name: "Gold", slug: "gold", color: "#b5a46d" },
                ],
                label: __("Warna Background", "weddingblocks"),
                enableAlpha: true,
                onChange: function (value) {
                  props.setAttributes({ backgroundColor: value });
                },
              },
            ],
          }),
        ),
        el(
          "div",
          useBlockProps({
            key: "editor-preview",
            className: "weddingblocks-guest-name-block",
            style: {
              backgroundColor: attributes.backgroundColor,
              textAlign: attributes.textAlign,
            },
          }),
          el(
            "span",
            { className: "wb-editor-badge" },
            el("span", { className: "wb-editor-badge-icon" }, "👤"),
            __("Nama Tamu", "weddingblocks"),
          ),
          attributes.showPrefix
            ? el(
              "p",
              {
                className: "guest-prefix-text",
                style: { color: attributes.textColor },
              },
              attributes.prefix,
            )
            : null,
          el(
            "h4",
            {
              className: "guest-name-text",
              style: {
                color: attributes.textColor,
                fontSize: nameFontSize,
              },
            },
            attributes.fallback,
          ),
          el(
            "small",
            {
              style: {
                display: "block",
                marginTop: "8px",
                color: "#888",
                fontSize: "10px",
              },
            },
            "💡 Akan diganti dengan ?to=NamaTamu di URL asli.",
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
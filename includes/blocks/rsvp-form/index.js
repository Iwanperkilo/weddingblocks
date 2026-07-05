(function (blocks, element, blockEditor, i18n) {
  var el = element.createElement;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var __ = i18n.__;

  blocks.registerBlockType("weddingblocks/rsvp-form", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var buttonColor = attributes.buttonColor;
      var inputBorderColor = attributes.inputBorderColor;

      var blockProps = useBlockProps({
        className: "weddingblocks-rsvp-form-editor",
        style: {
          "--wb-rsvp-button-color": buttonColor,
          "--wb-rsvp-input-border-color": inputBorderColor,
        },
      });

      return el(
        element.Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(PanelColorSettings, {
            title: __("Pengaturan Warna", "weddingblocks"),
            initialOpen: true,
            colorSettings: [
              {
                value: buttonColor,
                onChange: function (color) {
                  setAttributes({ buttonColor: color || "#b5a46d" });
                },
                label: __("Warna Tombol", "weddingblocks"),
              },
              {
                value: inputBorderColor,
                onChange: function (color) {
                  setAttributes({
                    inputBorderColor: color || "rgba(181, 164, 109, 0.3)",
                  });
                },
                label: __("Warna Border Input", "weddingblocks"),
              },
            ],
          }),
        ),
        el(
          "div",
          blockProps,
          el(
            "span",
            { className: "wb-editor-badge wb-editor-badge--block" },
            el("span", { className: "wb-editor-badge-icon" }, "📝"),
            __("Form RSVP", "weddingblocks"),
          ),
          el(
            "div",
            { className: "weddingblocks-rsvp-form-container" },
            el(
              "div",
              { className: "weddingblocks-form" },
              el(
                "div",
                { className: "form-group" },
                el("label", {}, __("Nama Anda", "weddingblocks")),
                el("input", {
                  type: "text",
                  placeholder: __("Ketik nama lengkap...", "weddingblocks"),
                  disabled: true,
                }),
              ),
              el(
                "div",
                { className: "form-group" },
                el("label", {}, __("Konfirmasi Kehadiran", "weddingblocks")),
                el(
                  "select",
                  { disabled: true },
                  el("option", {}, __("Pilih kehadiran...", "weddingblocks")),
                ),
              ),
              el(
                "div",
                { className: "form-group" },
                el("label", {}, __("Ucapan & Doa Restu", "weddingblocks")),
                el("textarea", {
                  rows: 4,
                  placeholder: __(
                    "Berikan ucapan selamat & doa restu terbaik...",
                    "weddingblocks",
                  ),
                  disabled: true,
                }),
              ),
              el(
                "button",
                { className: "weddingblocks-btn-gold", disabled: true },
                __("Kirim Konfirmasi", "weddingblocks"),
              ),
            ),
          ),
        ),
      );
    },
    save: function () {
      return null;
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n);

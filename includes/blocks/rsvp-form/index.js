(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var PanelBody = components.PanelBody;
  var ToggleControl = components.ToggleControl;
  var RangeControl = components.RangeControl;
  var TextControl = components.TextControl;
  var __ = i18n.__;

  blocks.registerBlockType("weddingblocks/rsvp-form", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var buttonColor = attributes.buttonColor;
      var inputBorderColor = attributes.inputBorderColor;
      var showGuestsField =
        attributes.showGuestsField === undefined
          ? true
          : attributes.showGuestsField;
      var maxGuests = attributes.maxGuests || 10;
      var labelName = attributes.labelName || __("Nama Anda", "weddingblocks");
      var labelAttendance =
        attributes.labelAttendance ||
        __("Konfirmasi Kehadiran", "weddingblocks");
      var labelMessage =
        attributes.labelMessage || __("Ucapan & Doa Restu", "weddingblocks");
      var placeholderMessage =
        attributes.placeholderMessage ||
        __(
          "Berikan ucapan selamat & doa restu terbaik untuk mempelai...",
          "weddingblocks",
        );
      var buttonText =
        attributes.buttonText || __("Kirim Konfirmasi", "weddingblocks");
      var successMessage =
        attributes.successMessage ||
        __("Konfirmasi RSVP berhasil dikirim!", "weddingblocks");
      var errorMessage =
        attributes.errorMessage ||
        __("Gagal mengirim konfirmasi. Silakan coba lagi.", "weddingblocks");
      var animPanel =
        typeof window.weddingblocksAnimationPanel === "function"
          ? window.weddingblocksAnimationPanel(attributes, setAttributes)
          : null;

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
            enableAlpha: true,
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
          el(
            PanelBody,
            { title: __("Pengaturan Form", "weddingblocks"), initialOpen: false },
            el(ToggleControl, {
              label: __("Tampilkan Jumlah Tamu (Pax)", "weddingblocks"),
              checked: showGuestsField,
              onChange: function (value) {
                setAttributes({ showGuestsField: value });
              },
            }),
            showGuestsField &&
            el(RangeControl, {
              label: __("Maks. Jumlah Tamu", "weddingblocks"),
              value: maxGuests,
              onChange: function (value) {
                setAttributes({ maxGuests: value || 10 });
              },
              min: 1,
              max: 50,
            }),
          ),
          el(
            PanelBody,
            { title: __("Teks & Label", "weddingblocks"), initialOpen: false },
            el(TextControl, {
              label: __("Label Nama", "weddingblocks"),
              value: labelName,
              onChange: function (value) {
                setAttributes({ labelName: value });
              },
            }),
            el(TextControl, {
              label: __("Label Kehadiran", "weddingblocks"),
              value: labelAttendance,
              onChange: function (value) {
                setAttributes({ labelAttendance: value });
              },
            }),
            el(TextControl, {
              label: __("Label Ucapan", "weddingblocks"),
              value: labelMessage,
              onChange: function (value) {
                setAttributes({ labelMessage: value });
              },
            }),
            el(TextControl, {
              label: __("Placeholder Ucapan", "weddingblocks"),
              value: placeholderMessage,
              onChange: function (value) {
                setAttributes({ placeholderMessage: value });
              },
            }),
            el(TextControl, {
              label: __("Teks Tombol", "weddingblocks"),
              value: buttonText,
              onChange: function (value) {
                setAttributes({ buttonText: value });
              },
            }),
          ),
          el(
            PanelBody,
            {
              title: __("Pesan Sukses & Error", "weddingblocks"),
              initialOpen: false,
            },
            el(TextControl, {
              label: __("Pesan Sukses", "weddingblocks"),
              value: successMessage,
              onChange: function (value) {
                setAttributes({ successMessage: value });
              },
            }),
            el(TextControl, {
              label: __("Pesan Error", "weddingblocks"),
              value: errorMessage,
              onChange: function (value) {
                setAttributes({ errorMessage: value });
              },
            }),
          ),
        ),
        animPanel,
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
                el("label", {}, labelName),
                el("input", {
                  type: "text",
                  placeholder: __("Ketik nama lengkap...", "weddingblocks"),
                  disabled: true,
                }),
              ),
              el(
                "div",
                { className: "form-group" },
                el("label", {}, labelAttendance),
                el(
                  "select",
                  { disabled: true },
                  el("option", {}, __("Pilih kehadiran...", "weddingblocks")),
                ),
              ),
              showGuestsField &&
              el(
                "div",
                { className: "form-group" },
                el(
                  "label",
                  {},
                  __("Jumlah Tamu (Pax)", "weddingblocks"),
                ),
                el("input", {
                  type: "number",
                  value: 1,
                  max: maxGuests,
                  disabled: true,
                }),
              ),
              el(
                "div",
                { className: "form-group" },
                el("label", {}, labelMessage),
                el("textarea", {
                  rows: 4,
                  placeholder: placeholderMessage,
                  disabled: true,
                }),
              ),
              el(
                "button",
                { className: "weddingblocks-btn-gold", disabled: true },
                buttonText,
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
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n,
);

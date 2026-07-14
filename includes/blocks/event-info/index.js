(function (blocks, element, blockEditor, i18n, components) {
  var el = element.createElement;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var TextControl = components.TextControl;
  var ToggleControl = components.ToggleControl;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var __ = i18n.__;

  // Mirrors weddingblocks_get_contrast_color() on the PHP side so the editor
  // preview matches what the frontend renders.
  function getContrastColor(hexColor) {
    if (!hexColor) {
      return "#ffffff";
    }
    var hex = hexColor.replace("#", "");
    if (hex.length === 3) {
      hex = hex
        .split("")
        .map(function (c) {
          return c + c;
        })
        .join("");
    }
    if (!/^[0-9a-fA-F]{6}$/.test(hex)) {
      return "#ffffff";
    }
    var r = parseInt(hex.substr(0, 2), 16) / 255;
    var g = parseInt(hex.substr(2, 2), 16) / 255;
    var b = parseInt(hex.substr(4, 2), 16) / 255;
    function channel(c) {
      return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    }
    var luminance =
      0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b);
    return luminance > 0.5 ? "#1f1f1f" : "#ffffff";
  }

  var colorPalette = [
    { name: __("White", "weddingblocks"), color: "#ffffff" },
    { name: __("Gold", "weddingblocks"), color: "#b5a46d" },
    { name: __("Dark Gold", "weddingblocks"), color: "#8a7a4f" },
    { name: __("Rose Gold", "weddingblocks"), color: "#b76e79" },
    { name: __("Maroon", "weddingblocks"), color: "#800000" },
    { name: __("Black", "weddingblocks"), color: "#2c2c2c" },
  ];

  blocks.registerBlockType("weddingblocks/event-info", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      var meta = wp.data.useSelect(function (select) {
        return select("core/editor").getEditedPostAttribute("meta") || {};
      });

      var akadTime =
        meta.weddingblocks_akad_time_label || "Pukul 08:00 - 10:00 WIB";
      var akadLocName =
        meta.weddingblocks_akad_location_name || "Masjid Agung Kota";
      var akadLocAddr =
        meta.weddingblocks_akad_location_address || "Jl. Cempaka No. 12";

      var resepsiTime =
        meta.weddingblocks_resepsi_time_label || "Pukul 11:00 - Selesai";
      var resepsiLocName =
        meta.weddingblocks_resepsi_location_name || "Gedung Serbaguna Indah";
      var resepsiLocAddr =
        meta.weddingblocks_resepsi_location_address || "Jl. Melati No. 45";

      var layoutVariation = attributes.layoutVariation || "horizontal";
      var primaryColor = attributes.primaryColor || "#b5a46d";
      var accentColor = attributes.accentColor || "#b5a46d";
      var textColor = attributes.textColor || "";

      var akadEventLabel =
        attributes.akadEventLabel || __("Akad Nikah", "weddingblocks");
      var resepsiEventLabel =
        attributes.resepsiEventLabel || __("Resepsi", "weddingblocks");

      var showMapsButton =
        attributes.showMapsButton === undefined
          ? true
          : attributes.showMapsButton;
      var showWhatsappButton =
        attributes.showWhatsappButton === undefined
          ? true
          : attributes.showWhatsappButton;
      var whatsappButtonLabel =
        attributes.whatsappButtonLabel ||
        __("Hubungi Admin (WhatsApp)", "weddingblocks");

      var wrapperClassName =
        "weddingblocks-event-info-editor weddingblocks-event-info-editor--" +
        layoutVariation;
      var wrapperStyle = {
        "--wb-event-primary-color": primaryColor,
        "--wb-event-accent-color": accentColor,
        "--wb-event-button-text-color": getContrastColor(primaryColor),
      };

      if (textColor) {
        wrapperStyle["--wb-event-ink"] = textColor;
      }

      var events = [
        {
          title: akadEventLabel,
          time: akadTime,
          name: akadLocName,
          addr: akadLocAddr,
        },
        {
          title: resepsiEventLabel,
          time: resepsiTime,
          name: resepsiLocName,
          addr: resepsiLocAddr,
        },
      ];

      var waActionElement = showWhatsappButton
        ? el(
          "div",
          { className: "weddingblocks-wa-action" },
          el(
            "span",
            { className: "weddingblocks-btn-gold weddingblocks-wa-button" },
            whatsappButtonLabel,
          ),
        )
        : null;

      function renderEventCard(event) {
        return el(
          "div",
          { className: "weddingblocks-event-card" },
          el("h3", {}, event.title),
          el(
            "p",
            {},
            el("strong", {}, event.time),
            el(
              "span",
              { className: "weddingblocks-event-card__meta" },
              event.name,
            ),
            el(
              "span",
              { className: "weddingblocks-event-card__meta" },
              event.addr,
            ),
          ),
          showMapsButton
            ? el(
              "div",
              { className: "weddingblocks-event-actions" },
              el(
                "span",
                { className: "weddingblocks-btn-gold weddingblocks-event-map-link" },
                __("Google Maps", "weddingblocks"),
              ),
            )
            : null,
        );
      }

      return el(
        "div",
        useBlockProps({
          className: wrapperClassName,
          style: wrapperStyle,
        }),
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            {
              title: __("Variasi Tata Letak", "weddingblocks"),
              initialOpen: true,
            },
            el(SelectControl, {
              label: __("Pilih Layout", "weddingblocks"),
              value: layoutVariation,
              options: [
                {
                  value: "vertical",
                  label: __("Vertical Stack (Mobile)", "weddingblocks"),
                },
                {
                  value: "horizontal",
                  label: __("Horizontal Split (Desktop)", "weddingblocks"),
                },
                {
                  value: "timeline",
                  label: __("Timeline View", "weddingblocks"),
                },
              ],
              onChange: function (value) {
                setAttributes({ layoutVariation: value });
              },
            }),
          ),
          el(
            PanelBody,
            {
              title: __("Label Acara", "weddingblocks"),
              initialOpen: false,
            },
            el(TextControl, {
              label: __("Label Acara Akad", "weddingblocks"),
              value: attributes.akadEventLabel || "",
              placeholder: __("Akad Nikah", "weddingblocks"),
              onChange: function (value) {
                setAttributes({ akadEventLabel: value });
              },
            }),
            el(TextControl, {
              label: __("Label Acara Resepsi", "weddingblocks"),
              value: attributes.resepsiEventLabel || "",
              placeholder: __("Resepsi", "weddingblocks"),
              onChange: function (value) {
                setAttributes({ resepsiEventLabel: value });
              },
            }),
          ),
          el(
            PanelBody,
            {
              title: __("Tombol Aksi", "weddingblocks"),
              initialOpen: false,
            },
            el(ToggleControl, {
              label: __("Tampilkan Tombol Google Maps", "weddingblocks"),
              checked: showMapsButton,
              onChange: function (value) {
                setAttributes({ showMapsButton: value });
              },
            }),
            el(ToggleControl, {
              label: __("Tampilkan Tombol WhatsApp", "weddingblocks"),
              checked: showWhatsappButton,
              onChange: function (value) {
                setAttributes({ showWhatsappButton: value });
              },
            }),
            showWhatsappButton
              ? el(TextControl, {
                label: __("Teks Tombol WhatsApp", "weddingblocks"),
                value: attributes.whatsappButtonLabel || "",
                placeholder: __(
                  "Hubungi Admin (WhatsApp)",
                  "weddingblocks",
                ),
                onChange: function (value) {
                  setAttributes({ whatsappButtonLabel: value });
                },
              })
              : null,
          ),
          el(PanelColorSettings, {
            title: __("Pengaturan Warna", "weddingblocks"),
            initialOpen: false,
            enableAlpha: true,
            colorSettings: [
              {
                value: primaryColor,
                colors: colorPalette,
                label: __("Warna Judul", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ primaryColor: value || "#b5a46d" });
                },
              },
              {
                value: accentColor,
                colors: colorPalette,
                label: __("Warna Timeline", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ accentColor: value || "#b5a46d" });
                },
              },
              {
                value: textColor,
                colors: colorPalette,
                label: __("Warna Teks Jam", "weddingblocks"),
                onChange: function (value) {
                  setAttributes({ textColor: value || "" });
                },
              },
            ],
          }),
        ),
        el(
          "span",
          { className: "wb-editor-badge wb-editor-badge--block" },
          el("span", { className: "wb-editor-badge-icon" }, "\uD83D\uDCC5"),
          __("Info Acara (Akad & Resepsi)", "weddingblocks"),
        ),
        layoutVariation === "timeline"
          ? el(
            "div",
            {
              className:
                "weddingblocks-event-columns weddingblocks-event-columns--timeline",
            },
            el(
              "div",
              { className: "weddingblocks-timeline" },
              events.map(function (event, index) {
                return el(
                  "div",
                  {
                    key: event.title,
                    className:
                      "weddingblocks-timeline-item " +
                      (index === 0
                        ? "weddingblocks-timeline-item--start"
                        : "weddingblocks-timeline-item--end"),
                  },
                  el("span", {
                    className: "weddingblocks-timeline-marker",
                    "aria-hidden": "true",
                  }),
                  el(
                    "div",
                    { className: "weddingblocks-timeline-content" },
                    renderEventCard(event),
                  ),
                );
              }),
            ),
          )
          : el(
            "div",
            {
              className:
                "weddingblocks-event-columns weddingblocks-event-columns--" +
                layoutVariation,
            },
            events.map(function (event) {
              return renderEventCard(event);
            }),
          ),
        waActionElement,
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
  window.wp.i18n,
  window.wp.components,
);
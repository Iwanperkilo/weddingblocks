(function (blocks, element, blockEditor, i18n, components) {
  var el = element.createElement;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var __ = i18n.__;

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
      var wrapperClassName =
        "weddingblocks-event-info-editor weddingblocks-event-info-editor--" +
        layoutVariation;
      var wrapperStyle = {
        "--wb-event-primary-color": primaryColor,
        "--wb-event-accent-color": accentColor,
      };

      var events = [
        {
          title: __("Akad Nikah", "weddingblocks"),
          time: akadTime,
          name: akadLocName,
          addr: akadLocAddr,
        },
        {
          title: __("Resepsi", "weddingblocks"),
          time: resepsiTime,
          name: resepsiLocName,
          addr: resepsiLocAddr,
        },
      ];

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
          el(PanelColorSettings, {
            title: __("Pengaturan Warna", "weddingblocks"),
            initialOpen: false,
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
            ],
          }),
        ),
        el(
          "span",
          { className: "wb-editor-badge wb-editor-badge--block" },
          el("span", { className: "wb-editor-badge-icon" }, "ðŸ“…"),
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

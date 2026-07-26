(function (blocks, blockEditor, element, components, i18n) {
  var el = element.createElement;
  var __ = i18n.__;
  var useBlockProps = blockEditor.useBlockProps;
  var InnerBlocks = blockEditor.InnerBlocks;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var ToggleControl = components.ToggleControl;

  var TEMPLATE = [["weddingblocks/decorative-layer", {}]];

  blocks.registerBlockType("weddingblocks/decorative-wrapper", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      var blockProps = useBlockProps({
        className: "wb-decor-wrapper",
        style: { position: "relative" },
      });

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Pengaturan Dekorasi", "weddingblocks") },
            el(SelectControl, {
              label: __("Jenis Dekorasi", "weddingblocks"),
              value: attributes.decorationType,
              options: [
                {
                  label: __("Salju", "weddingblocks"),
                  value: "dots",
                },
                { label: __("Kupu-kupu", "weddingblocks"), value: "butterfly" },
                { label: __("Burung", "weddingblocks"), value: "bird" },
              ],
              onChange: function (value) {
                setAttributes({ decorationType: value });
              },
            }),
            el(SelectControl, {
              label: __("Kepadatan", "weddingblocks"),
              value: attributes.density,
              options: [
                { label: __("Rendah", "weddingblocks"), value: "low" },
                { label: __("Sedang", "weddingblocks"), value: "medium" },
                { label: __("Tinggi", "weddingblocks"), value: "high" },
              ],
              onChange: function (value) {
                setAttributes({ density: value });
              },
            }),
            el(SelectControl, {
              label: __("Posisi Lapisan", "weddingblocks"),
              value: attributes.layer,
              options: [
                {
                  label: __("Di Belakang Konten", "weddingblocks"),
                  value: "behind",
                },
                {
                  label: __("Di Depan Konten", "weddingblocks"),
                  value: "front",
                },
              ],
              onChange: function (value) {
                setAttributes({ layer: value });
              },
            }),
            el(ToggleControl, {
              label: __("Aktifkan di Mobile", "weddingblocks"),
              checked: attributes.enableOnMobile,
              help: __(
                "Matikan jika ingin hemat performa di perangkat low-end.",
                "weddingblocks",
              ),
              onChange: function (value) {
                setAttributes({ enableOnMobile: value });
              },
            }),
          ),
        ),
        el(InnerBlocks, { template: TEMPLATE, templateLock: false }),
      );
    },
    save: function () {
      return el(InnerBlocks.Content);
    },
  });
})(
  window.wp.blocks,
  window.wp.blockEditor,
  window.wp.element,
  window.wp.components,
  window.wp.i18n,
);
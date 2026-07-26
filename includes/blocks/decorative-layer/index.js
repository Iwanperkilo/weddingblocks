(function (blocks, blockEditor, element, i18n) {
  var el = element.createElement;
  var __ = i18n.__;
  var useBlockProps = blockEditor.useBlockProps;

  var LABELS = {
    dots: __("Salju", "weddingblocks"),
    butterfly: __("Kupu-kupu", "weddingblocks"),
    bird: __("Burung", "weddingblocks"),
  };

  blocks.registerBlockType("weddingblocks/decorative-layer", {
    edit: function (props) {
      var context = props.context || {};
      var decorationType = context["weddingblocks/decorationType"] || "dots";
      var density = context["weddingblocks/density"] || "medium";
      var layer = context["weddingblocks/layer"] || "behind";

      var blockProps = useBlockProps({
        className: "wb-decor-layer-placeholder",
        style: {
          position: "absolute",
          inset: 0,
          zIndex: layer === "front" ? 5 : 0,
          pointerEvents: "none",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          border: "1px dashed rgba(0,0,0,0.15)",
        },
      });

      return el(
        "div",
        blockProps,
        el(
          "span",
          {
            style: {
              fontSize: "11px",
              color: "rgba(0,0,0,0.4)",
              background: "rgba(255,255,255,0.6)",
              padding: "2px 6px",
              borderRadius: "4px",
            },
          },
          "✨ " + (LABELS[decorationType] || decorationType) + " · " + density,
        ),
      );
    },
    save: function () {
      return null; // Dynamic block, rendered server-side via render.php
    },
  });
})(window.wp.blocks, window.wp.blockEditor, window.wp.element, window.wp.i18n);
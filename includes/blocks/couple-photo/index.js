(function (blocks, element, blockEditor, components, i18n) {
  var el = element.createElement;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var FocalPointPicker = components.FocalPointPicker;
  var SelectControl = components.SelectControl;
  var RangeControl = components.RangeControl;
  var Button = components.Button;
  var useBlockProps = blockEditor.useBlockProps;
  var PanelColorSettings = blockEditor.PanelColorSettings;
  var __ = i18n.__;

  blocks.registerBlockType("weddingblocks/couple-photo", {
    edit: function (props) {
      var attributes = props.attributes;
      var meta = wp.data.useSelect(function (select) {
        var editor = select("core/editor");
        if (!editor || typeof editor.getEditedPostAttribute !== "function")
          return {};
        return editor.getEditedPostAttribute("meta") || {};
      });

      var role = attributes.role || "groom";
      var shape = attributes.shape || "circle";
      var size = attributes.size || 200;
      var showFrame = attributes.showFrame !== false;
      var frameColor = attributes.frameColor || "";
      var frameWidth = attributes.frameWidth || 3;
      var align = attributes.align || "center";
      var focalPoint = attributes.photoFocalPoint || { x: 0.5, y: 0.5 };
      var zoom = attributes.photoZoom || 100;

      var photo, name, fallback, roleLabel;
      if (role === "bride") {
        photo = attributes.bridePhoto || meta.weddingblocks_bride_photo || "";
        name = attributes.brideName || meta.weddingblocks_bride_name || "";
        fallback = __("Mempelai Wanita", "weddingblocks");
        roleLabel = __("Wanita", "weddingblocks");
      } else {
        photo = attributes.groomPhoto || meta.weddingblocks_groom_photo || "";
        name = attributes.groomName || meta.weddingblocks_groom_name || "";
        fallback = __("Mempelai Pria", "weddingblocks");
        roleLabel = __("Pria", "weddingblocks");
      }
      if (!name) name = fallback;

      var styleAttr = attributes.style || {};
      var borderObj = styleAttr.border || {};
      var rawRadius = borderObj.radius;

      var borderRadiusStyle = "";
      if (typeof rawRadius === "string" && rawRadius.trim() !== "") {
        borderRadiusStyle = rawRadius;
      } else if (typeof rawRadius === "object" && rawRadius !== null) {
        var tl =
          rawRadius.topLeft !== undefined
            ? rawRadius.topLeft
            : rawRadius.top || "0";
        var tr =
          rawRadius.topRight !== undefined
            ? rawRadius.topRight
            : rawRadius.right || "0";
        var br =
          rawRadius.bottomRight !== undefined
            ? rawRadius.bottomRight
            : rawRadius.bottom || "0";
        var bl =
          rawRadius.bottomLeft !== undefined
            ? rawRadius.bottomLeft
            : rawRadius.left || "0";
        borderRadiusStyle = tl + " " + tr + " " + br + " " + bl;
      } else if (shape === "rounded") {
        borderRadiusStyle = "16px";
      } else if (shape === "square") {
        borderRadiusStyle = "0px";
      } else {
        borderRadiusStyle = "50%";
      }

      // Border Color
      var borderColorStyle = "";
      if (borderObj.color) {
        borderColorStyle = borderObj.color;
      } else if (attributes.borderColor) {
        borderColorStyle =
          "var(--wp--preset--color--" + attributes.borderColor + ")";
      } else if (showFrame && frameColor) {
        borderColorStyle = frameColor;
      }

      // Border Width
      var borderWidthStyle = "";
      if (borderObj.width) {
        if (typeof borderObj.width === "object") {
          borderWidthStyle =
            (borderObj.width.top || "0") +
            " " +
            (borderObj.width.right || "0") +
            " " +
            (borderObj.width.bottom || "0") +
            " " +
            (borderObj.width.left || "0");
        } else {
          borderWidthStyle = borderObj.width;
        }
      } else if (
        styleAttr.border &&
        Object.prototype.hasOwnProperty.call(styleAttr.border, "width")
      ) {
        borderWidthStyle = "0px";
      } else if (showFrame) {
        borderWidthStyle = frameWidth + "px";
      } else {
        borderWidthStyle = "0px";
      }

      // Border Style
      var borderStyleVal =
        borderObj.style ||
        (borderWidthStyle &&
        borderWidthStyle !== "0px" &&
        borderWidthStyle !== "0"
          ? "solid"
          : "");

      var placeholderSvg =
        'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
      var displayPhoto = photo || placeholderSvg;
      var imgStyle = {
        width: size + "px",
        height: size + "px",
        borderRadius: borderRadiusStyle,
      };
      if (borderWidthStyle) {
        imgStyle.borderWidth = borderWidthStyle;
      }
      if (borderColorStyle) {
        imgStyle.borderColor = borderColorStyle;
      }
      if (borderStyleVal) {
        imgStyle.borderStyle = borderStyleVal;
      }

      var focalPositionStr =
        Math.round(focalPoint.x * 100) +
        "% " +
        Math.round(focalPoint.y * 100) +
        "%";
      var photoImgStyle = {
        objectPosition: focalPositionStr,
        transformOrigin: focalPositionStr,
        "--wb-photo-zoom": zoom / 100,
      };
      var wrapperClass =
        "weddingblocks-atomic-couple-photo role-" +
        role +
        " shape-" +
        shape +
        " align-" +
        align +
        (showFrame ||
        (borderWidthStyle &&
          borderWidthStyle !== "0px" &&
          borderWidthStyle !== "0")
          ? " has-frame"
          : " no-frame");

      var animPanel =
        typeof window.weddingblocksAnimationPanel === "function"
          ? window.weddingblocksAnimationPanel(attributes, props.setAttributes)
          : null;

      return [
        el(
          InspectorControls,
          { key: "inspector" },
          el(
            PanelBody,
            {
              title: __("Pengaturan Foto Mempelai", "weddingblocks"),
              initialOpen: true,
            },
            el(SelectControl, {
              label: __("Mempelai", "weddingblocks"),
              value: role,
              options: [
                { label: __("Mempelai Pria", "weddingblocks"), value: "groom" },
                {
                  label: __("Mempelai Wanita", "weddingblocks"),
                  value: "bride",
                },
              ],
              onChange: function (v) {
                props.setAttributes({ role: v });
              },
            }),
            el(SelectControl, {
              label: __("Perataan", "weddingblocks"),
              value: align,
              options: [
                { label: __("Kiri", "weddingblocks"), value: "left" },
                { label: __("Tengah", "weddingblocks"), value: "center" },
                { label: __("Kanan", "weddingblocks"), value: "right" },
              ],
              onChange: function (v) {
                props.setAttributes({ align: v });
              },
            }),
            el(RangeControl, {
              label: __("Ukuran Foto (px)", "weddingblocks"),
              value: size,
              min: 40,
              max: 800,
              onChange: function (v) {
                props.setAttributes({ size: v });
              },
            }),
            el(FocalPointPicker, {
              label: __("Fokus Foto", "weddingblocks"),
              url: displayPhoto,
              value: focalPoint,
              onChange: function (v) {
                props.setAttributes({ photoFocalPoint: v });
              },
            }),
            el(RangeControl, {
              label: __("Zoom Foto (%)", "weddingblocks"),
              value: zoom,
              min: 100,
              max: 300,
              onChange: function (v) {
                props.setAttributes({ photoZoom: v !== undefined ? v : 100 });
              },
            }),
          ),
        ),
        animPanel,
        el(
          "div",
          useBlockProps({ key: "preview", className: wrapperClass }),
          el(
            "span",
            { className: "wb-editor-badge" },
            el(
              "span",
              { className: "wb-editor-badge-icon" },
              "\uD83D\uDDBC\uFE0F",
            ),
            __("Foto " + roleLabel, "weddingblocks"),
          ),
          el(
            "figure",
            {
              className:
                "atomic-photo shape-" +
                shape +
                (showFrame ? " has-frame" : " no-frame"),
              style: imgStyle,
            },
            el("img", {
              src: displayPhoto,
              alt: name,
              className: photo ? "" : "atomic-photo-placeholder",
              style: photo ? photoImgStyle : undefined,
            }),
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

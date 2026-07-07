(function (blocks, element, blockEditor, i18n, components) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var __ = i18n.__;
    var PanelBody = components.PanelBody;
    var ToggleControl = components.ToggleControl;
    var TextControl = components.TextControl;
    var RangeControl = components.RangeControl;
    var SelectControl = components.SelectControl;
    var ColorPalette = components.ColorPalette;

    // Color palette for label text color
    var LABEL_TEXT_COLORS = [
      { name: "Hitam", color: "#2c2c2c" },
      { name: "Abu Gelap", color: "#555555" },
      { name: "Abu Muda", color: "#888888" },
      { name: "Putih", color: "#ffffff" },
      { name: "Emas", color: "#b5a46d" },
      { name: "Emas Gelap", color: "#8a7a4f" },
    ];

    blocks.registerBlockType('weddingblocks/couple-info', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });
            var placeholderSvg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

            var groomName = meta.weddingblocks_groom_name || __('Mempelai Pria', 'weddingblocks');
            var groomParents = meta.weddingblocks_groom_parents || __('Putra dari Bapak & Ibu Orang Tua Pria', 'weddingblocks');
            var groomPhoto = meta.weddingblocks_groom_photo || placeholderSvg;

            var brideName = meta.weddingblocks_bride_name || __('Mempelai Wanita', 'weddingblocks');
            var brideParents = meta.weddingblocks_bride_parents || __('Putri dari Bapak & Ibu Orang Tua Wanita', 'weddingblocks');
            var bridePhoto = meta.weddingblocks_bride_photo || placeholderSvg;

            // Determine display order based on swapCouple attribute
            var firstName = attributes.swapCouple ? groomName : brideName;
            var firstParents = attributes.swapCouple ? groomParents : brideParents;
            var firstPhoto = attributes.swapCouple ? groomPhoto : bridePhoto;
            var firstLabel = attributes.swapCouple ? __('Mempelai Pria', 'weddingblocks') : __('Mempelai Wanita', 'weddingblocks');

            var secondName = attributes.swapCouple ? brideName : groomName;
            var secondParents = attributes.swapCouple ? brideParents : groomParents;
            var secondPhoto = attributes.swapCouple ? bridePhoto : groomPhoto;
            var secondLabel = attributes.swapCouple ? __('Mempelai Wanita', 'weddingblocks') : __('Mempelai Pria', 'weddingblocks');
            var showParentsLabel = attributes.showParentsLabel;
            var parentsLabelGroom = attributes.parentsLabelGroom;
            var parentsLabelBride = attributes.parentsLabelBride;
            var parentsLabelFontSize = attributes.parentsLabelFontSize;
            var parentsLabelFontFamily = attributes.parentsLabelFontFamily;
            var parentsLabelTextColor = attributes.parentsLabelTextColor;
            var nameFontSize = attributes.nameFontSize || 24;

            // Font family mapping for editor
            var fontFamilyCss = "Georgia, serif";
            if ('playfair' === parentsLabelFontFamily) {
                fontFamilyCss = "'Playfair Display', Georgia, serif";
            } else if ('greatvibes' === parentsLabelFontFamily) {
                fontFamilyCss = "'Great Vibes', cursive";
            } else if ('montserrat' === parentsLabelFontFamily) {
                fontFamilyCss = "'Montserrat', sans-serif";
            } else if ('georgia' === parentsLabelFontFamily) {
                fontFamilyCss = "Georgia, serif";
            } else if ('sans-serif' === parentsLabelFontFamily) {
                fontFamilyCss = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
            }
            var parentsLabelStyle = {
                fontSize: parentsLabelFontSize + 'px',
                fontFamily: fontFamilyCss,
                color: parentsLabelTextColor,
            };
            var nameStyle = {
                fontSize: nameFontSize + 'px',
            };

            // Layout class
            var layoutClass = attributes.layout === 'vertical' ? 'weddingblocks-couple-columns--vertical' : '';

            return el('div', useBlockProps({ className: 'weddingblocks-couple-info-editor' }), 
                el(InspectorControls, {}, 
                    el(PanelBody, { title: __('Pengaturan Layout dan Urutan', 'weddingblocks'), initialOpen: true }, 
                        el(ToggleControl, {
                            label: __('Tukar Posisi Mempelai', 'weddingblocks'),
                            help: attributes.swapCouple ? __('Mempelai Pria ditampilkan di kiri/atas', 'weddingblocks') : __('Mempelai Wanita ditampilkan di kiri/atas', 'weddingblocks'),
                            checked: attributes.swapCouple,
                            onChange: function (value) {
                                setAttributes({ swapCouple: value });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Tata Letak', 'weddingblocks'),
                            value: attributes.layout,
                            options: [
                                { value: 'horizontal', label: __('Horizontal', 'weddingblocks') },
                                { value: 'vertical', label: __('Vertical', 'weddingblocks') }
                            ],
                            onChange: function (value) {
                                setAttributes({ layout: value });
                            }
                        })
                    ),
                    el(PanelBody, { title: __('Nama Mempelai', 'weddingblocks'), initialOpen: false },
                        el(RangeControl, {
                            label: __('Ukuran Font Nama', 'weddingblocks'),
                            value: nameFontSize,
                            onChange: function (value) {
                                setAttributes({ nameFontSize: value });
                            },
                            min: 16,
                            max: 64,
                            step: 1,
                        })
                    ),
                    el(PanelBody, { title: __('Label & Nama Orang Tua', 'weddingblocks'), initialOpen: false }, 
                        el(ToggleControl, {
                            label: __('Tampilkan Label Orang Tua', 'weddingblocks'),
                            checked: showParentsLabel,
                            onChange: function (value) {
                                setAttributes({ showParentsLabel: value });
                            }
                        }),
                        showParentsLabel && el(TextControl, {
                            label: __('Label Orang Tua Pria', 'weddingblocks'),
                            value: parentsLabelGroom,
                            onChange: function (value) {
                                setAttributes({ parentsLabelGroom: value });
                            }
                        }),
                        showParentsLabel && el(TextControl, {
                            label: __('Label Orang Tua Wanita', 'weddingblocks'),
                            value: parentsLabelBride,
                            onChange: function (value) {
                                setAttributes({ parentsLabelBride: value });
                            }
                        }),
                        showParentsLabel && el(RangeControl, {
                            label: __('Ukuran Font', 'weddingblocks'),
                            value: parentsLabelFontSize,
                            onChange: function (value) {
                                setAttributes({ parentsLabelFontSize: value });
                            },
                            min: 10,
                            max: 50,
                            step: 1,
                        }),
                        showParentsLabel && el(SelectControl, {
                            label: __('Gaya Font', 'weddingblocks'),
                            value: parentsLabelFontFamily,
                            options: [
                                { label: 'Playfair Display', value: 'playfair' },
                                { label: 'Great Vibes', value: 'greatvibes' },
                                { label: 'Montserrat', value: 'montserrat' },
                                { label: 'Georgia', value: 'georgia' },
                                { label: 'Sans-serif', value: 'sans-serif' },
                            ],
                            onChange: function (value) {
                                setAttributes({ parentsLabelFontFamily: value });
                            }
                        }),
                        showParentsLabel && el('div', { className: 'components-base-control' },
                            el('label', { className: 'components-base-control__label' }, __('Warna Teks', 'weddingblocks')),
                            el(ColorPalette, {
                                value: parentsLabelTextColor,
                                colors: LABEL_TEXT_COLORS,
                                onChange: function (value) {
                                    setAttributes({ parentsLabelTextColor: value });
                                },
                            })
                        )
                    )
                ),
                el('div', { className: 'weddingblocks-couple-columns ' + layoutClass },
                    el('div', { className: 'weddingblocks-couple-column' },
                        el('div', { className: 'weddingblocks-avatar' },
                            el('img', { src: firstPhoto, alt: firstName })
                        ),
                        el('h3', { style: nameStyle }, firstName),
                        showParentsLabel && el('p', { className: 'weddingblocks-parents-info' },
                            el('span', { className: 'weddingblocks-parents-label', style: parentsLabelStyle },
                                attributes.swapCouple ? parentsLabelGroom : parentsLabelBride),
                            el('span', { className: 'weddingblocks-parents-names', style: parentsLabelStyle },
                                firstParents)
                        ),
                        !showParentsLabel && el('p', {}, firstParents)
                    ),
                    el('div', { className: 'weddingblocks-couple-column weddingblocks-separator-column' },
                        el('p', { className: 'weddingblocks-ampersand' }, '&')
                    ),
                    el('div', { className: 'weddingblocks-couple-column' },
                        el('div', { className: 'weddingblocks-avatar' },
                            el('img', { src: secondPhoto, alt: secondName })
                        ),
                        el('h3', { style: nameStyle }, secondName),
                        showParentsLabel && el('p', { className: 'weddingblocks-parents-info' },
                            el('span', { className: 'weddingblocks-parents-label', style: parentsLabelStyle },
                                attributes.swapCouple ? parentsLabelBride : parentsLabelGroom),
                            el('span', { className: 'weddingblocks-parents-names', style: parentsLabelStyle },
                                secondParents)
                        ),
                        !showParentsLabel && el('p', {}, secondParents)
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n, window.wp.components);

(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var TextControl = components.TextControl;
    var Button = components.Button;
    var RangeControl = components.RangeControl;
    var ColorPalette = components.ColorPalette;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;
    var customColors = [
        { name: 'Hitam', color: '#2c2c2c' },
        { name: 'Abu Gelap', color: '#555555' },
        { name: 'Abu Muda', color: '#888888' },
        { name: 'Putih', color: '#ffffff' },
        { name: 'Emas', color: '#b5a46d' },
        { name: 'Emas Gelap', color: '#8a7a4f' },
    ];
    blocks.registerBlockType('weddingblocks/couple-parents', {
        edit: function (props) {
            var attributes = props.attributes;
            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });
            var role = attributes.role || 'groom';
            var label = attributes.label || '';
            var showLabel = attributes.showLabel !== false;
            var align = attributes.align || 'center';
            var fontSize = attributes.fontSize || 17;
            var fontFamily = attributes.fontFamily || 'playfair';
            var textColor = attributes.textColor || '#333333';
            var parents, defaultLabel, fallback, roleLabel;
            if (role === 'bride') {
                parents = attributes.brideParents || meta.weddingblocks_bride_parents || '';
                defaultLabel = __('Putri dari', 'weddingblocks');
                fallback = __('Bapak & Ibu Orang Tua Wanita', 'weddingblocks');
                roleLabel = __('Wanita', 'weddingblocks');
            } else {
                parents = attributes.groomParents || meta.weddingblocks_groom_parents || '';
                defaultLabel = __('Putra dari', 'weddingblocks');
                fallback = __('Bapak & Ibu Orang Tua Pria', 'weddingblocks');
                roleLabel = __('Pria', 'weddingblocks');
            }
            if (!parents) parents = fallback;
            if (!label) label = defaultLabel;
            var fontFamilyCSS = "Georgia, serif";
            if (fontFamily === 'playfair') {
                fontFamilyCSS = "'Playfair Display', Georgia, serif";
            } else if (fontFamily === 'greatvibes') {
                fontFamilyCSS = "'Great Vibes', cursive";
            } else if (fontFamily === 'montserrat') {
                fontFamilyCSS = "'Montserrat', sans-serif";
            } else if (fontFamily === 'georgia') {
                fontFamilyCSS = "Georgia, serif";
            } else if (fontFamily === 'sans-serif') {
                fontFamilyCSS = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
            }
            var previewStyle = {
                fontSize: fontSize + 'px',
                fontFamily: fontFamilyCSS,
                color: textColor
            };
            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Pengaturan Orang Tua', 'weddingblocks'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Mempelai', 'weddingblocks'),
                            value: role,
                            options: [
                                { label: __('Mempelai Pria', 'weddingblocks'), value: 'groom' },
                                { label: __('Mempelai Wanita', 'weddingblocks'), value: 'bride' }
                            ],
                            onChange: function (v) { props.setAttributes({ role: v }); }
                        }),
                        el(SelectControl, {
                            label: __('Perataan', 'weddingblocks'),
                            value: align,
                            options: [
                                { label: __('Kiri', 'weddingblocks'), value: 'left' },
                                { label: __('Tengah', 'weddingblocks'), value: 'center' },
                                { label: __('Kanan', 'weddingblocks'), value: 'right' }
                            ],
                            onChange: function (v) { props.setAttributes({ align: v }); }
                        }),
                        el(TextControl, {
                            label: __('Label (contoh: Putra dari)', 'weddingblocks'),
                            value: label,
                            onChange: function (v) { props.setAttributes({ label: v }); }
                        }),
                        el(Button, {
                            isSecondary: showLabel,
                            isTertiary: !showLabel,
                            onClick: function () { props.setAttributes({ showLabel: !showLabel }); }
                        }, showLabel ? __('Tampilkan Label: ON', 'weddingblocks') : __('Tampilkan Label: OFF', 'weddingblocks'))
                    ),
                    el(PanelBody, { title: __('Pengaturan Tipografi', 'weddingblocks'), initialOpen: false },
                        el(RangeControl, {
                            label: __('Ukuran Font (px)', 'weddingblocks'),
                            value: fontSize,
                            min: 10,
                            max: 60,
                            onChange: function (v) { props.setAttributes({ fontSize: v }); }
                        }),
                        el(SelectControl, {
                            label: __('Jenis Font', 'weddingblocks'),
                            value: fontFamily,
                            options: [
                                { label: __('Playfair Display (Serif Elegant)', 'weddingblocks'), value: 'playfair' },
                                { label: __('Great Vibes (Calligraphy)', 'weddingblocks'), value: 'greatvibes' },
                                { label: __('Montserrat (Modern Sans-serif)', 'weddingblocks'), value: 'montserrat' },
                                { label: __('Georgia (Classic Serif)', 'weddingblocks'), value: 'georgia' },
                                { label: __('System Sans-serif', 'weddingblocks'), value: 'sans-serif' }
                            ],
                            onChange: function (v) { props.setAttributes({ fontFamily: v }); }
                        }),
                        el('p', { style: { marginBottom: '5px', fontWeight: '500' } }, __('Warna Teks', 'weddingblocks')),
                        el(ColorPalette, {
                            value: textColor,
                            colors: customColors,
                            onChange: function (v) { props.setAttributes({ textColor: v || '#333333' }); }
                        })
                    )
                ),
                el('div', useBlockProps({ key: 'preview', className: 'weddingblocks-atomic-couple-parents role-' + role + ' align-' + align }),
                    showLabel ? el('span', { className: 'atomic-parents-label', style: previewStyle }, label) : null,
                    el('span', { className: 'atomic-parents-names', style: previewStyle }, parents)
                )
            ];
        },
        save: function () { return null; }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

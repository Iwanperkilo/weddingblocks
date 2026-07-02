(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/guest-name', {
        edit: function (props) {
            var attributes = props.attributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Pengaturan Nama Tamu', 'weddingblocks'), initialOpen: true },
                        el(TextControl, {
                            label: __('Teks Pembuka (Prefix)', 'weddingblocks'),
                            value: attributes.prefix,
                            onChange: function (value) {
                                props.setAttributes({ prefix: value });
                            }
                        }),
                        el(TextControl, {
                            label: __('Teks Cadangan (Fallback)', 'weddingblocks'),
                            value: attributes.fallback,
                            onChange: function (value) {
                                props.setAttributes({ fallback: value });
                            },
                            help: __('Ditampilkan jika link undangan diakses tanpa parameter ?to=NamaTamu.', 'weddingblocks')
                        }),
                        el(components.BaseControl, { label: __('Warna Teks', 'weddingblocks') },
                            el(components.ColorPalette, {
                                colors: [
                                    { name: 'Black', slug: 'black', color: '#000000' },
                                    { name: 'Dark Gray', slug: 'dark-gray', color: '#333333' },
                                    { name: 'Medium Gray', slug: 'medium-gray', color: '#777777' },
                                    { name: 'Light Gray', slug: 'light-gray', color: '#eeeeee' },
                                    { name: 'White', slug: 'white', color: '#ffffff' },
                                    { name: 'Gold', slug: 'gold', color: '#b5a46d' }
                                ],
                                value: attributes.textColor,
                                onChange: function (value) {
                                    props.setAttributes({ textColor: value });
                                }
                            })
                        ),
                        el(components.BaseControl, { label: __('Warna Latar Belakang', 'weddingblocks') },
                            el(components.ColorPalette, {
                                colors: [
                                    { name: 'Transparent', slug: 'transparent', color: 'transparent' },
                                    { name: 'White', slug: 'white', color: '#ffffff' },
                                    { name: 'Light Gray', slug: 'light-gray', color: '#eeeeee' },
                                    { name: 'Gold', slug: 'gold', color: '#b5a46d' }
                                ],
                                value: attributes.backgroundColor,
                                onChange: function (value) {
                                    props.setAttributes({ backgroundColor: value });
                                }
                            })
                        )
                    )
                ),
                el('div', useBlockProps({ key: 'editor-preview', className: 'weddingblocks-guest-name-block', style: { backgroundColor: attributes.backgroundColor } }),
                    el('span', { className: 'wb-editor-badge' },
                        el('span', { className: 'wb-editor-badge-icon' }, '👤'),
                        __('Nama Tamu', 'weddingblocks')
                    ),
                    el('p', { className: 'guest-prefix-text', style: { color: attributes.textColor } }, attributes.prefix),
                    el('h4', { className: 'guest-name-text', style: { color: attributes.textColor } }, attributes.fallback),
                    el('small', { style: { display: 'block', marginTop: '8px', color: '#888', fontSize: '10px' } },
                        '💡 Akan diganti dengan ?to=NamaTamu di URL asli.'
                    )
                )
            ];
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

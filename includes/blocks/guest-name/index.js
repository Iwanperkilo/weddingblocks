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
                        })
                    )
                ),
                el('div', useBlockProps({ key: 'editor-preview', className: 'weddingblocks-guest-name-block' }),
                    el('span', { className: 'wb-editor-badge' },
                        el('span', { className: 'wb-editor-badge-icon' }, '👤'),
                        __('Nama Tamu', 'weddingblocks')
                    ),
                    el('p', { className: 'guest-prefix-text' }, attributes.prefix),
                    el('h4', { className: 'guest-name-text' }, attributes.fallback),
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

(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var TextControl = components.TextControl;
    var Button = components.Button;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

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
                        el(TextControl, {
                            label: __('Label (contoh: Putra dari)', 'weddingblocks'),
                            value: label,
                            onChange: function (v) { props.setAttributes({ label: v }); }
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
                        el(Button, {
                            isSecondary: showLabel,
                            isTertiary: !showLabel,
                            onClick: function () { props.setAttributes({ showLabel: !showLabel }); }
                        }, showLabel ? __('Tampilkan Label: ON', 'weddingblocks') : __('Tampilkan Label: OFF', 'weddingblocks'))
                    )
                ),
                el('div', useBlockProps({ key: 'preview', className: 'weddingblocks-atomic-couple-parents role-' + role + ' align-' + align }),
                    el('span', { className: 'wb-editor-badge' },
                        el('span', { className: 'wb-editor-badge-icon' }, '👪'),
                        __('Orang Tua ' + roleLabel, 'weddingblocks')
                    ),
                    showLabel ? el('span', { className: 'atomic-parents-label' }, label) : null,
                    el('span', { className: 'atomic-parents-names' }, parents)
                )
            ];
        },
        save: function () { return null; }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

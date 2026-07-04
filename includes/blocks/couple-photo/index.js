(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var RangeControl = components.RangeControl;
    var Button = components.Button;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/couple-photo', {
        edit: function (props) {
            var attributes = props.attributes;
            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });
            var role = attributes.role || 'groom';
            var shape = attributes.shape || 'circle';
            var size = attributes.size || 200;
            var showFrame = attributes.showFrame !== false;
            var align = attributes.align || 'center';

            var photo, name, fallback, roleLabel;
            if (role === 'bride') {
                photo = attributes.bridePhoto || meta.weddingblocks_bride_photo || '';
                name = attributes.brideName || meta.weddingblocks_bride_name || '';
                fallback = __('Mempelai Wanita', 'weddingblocks');
                roleLabel = __('Wanita', 'weddingblocks');
            } else {
                photo = attributes.groomPhoto || meta.weddingblocks_groom_photo || '';
                name = attributes.groomName || meta.weddingblocks_groom_name || '';
                fallback = __('Mempelai Pria', 'weddingblocks');
                roleLabel = __('Pria', 'weddingblocks');
            }
            if (!name) name = fallback;

            var placeholderSvg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
            var displayPhoto = photo || placeholderSvg;

            var imgStyle = {
                width: size + 'px',
                height: size + 'px',
            };
            var wrapperClass = 'weddingblocks-atomic-couple-photo role-' + role + ' shape-' + shape + ' align-' + align + (showFrame ? ' has-frame' : ' no-frame');

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Pengaturan Foto Mempelai', 'weddingblocks'), initialOpen: true },
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
                        el(SelectControl, {
                            label: __('Style Foto', 'weddingblocks'),
                            value: shape,
                            options: [
                                { label: __('Bulat (Circle)', 'weddingblocks'), value: 'circle' },
                                { label: __('Sudut Membulat (Rounded)', 'weddingblocks'), value: 'rounded' },
                                { label: __('Kotak (Square)', 'weddingblocks'), value: 'square' }
                            ],
                            onChange: function (v) { props.setAttributes({ shape: v }); }
                        }),
                        el(RangeControl, {
                            label: __('Ukuran Foto (px)', 'weddingblocks'),
                            value: size,
                            min: 40,
                            max: 800,
                            onChange: function (v) { props.setAttributes({ size: v }); }
                        }),
                        el(Button, {
                            isSecondary: showFrame,
                            isTertiary: !showFrame,
                            onClick: function () { props.setAttributes({ showFrame: !showFrame }); }
                        }, showFrame ? __('Tampilkan Bingkai: ON', 'weddingblocks') : __('Tampilkan Bingkai: OFF', 'weddingblocks'))
                    )
                ),
                el('div', useBlockProps({ key: 'preview', className: wrapperClass }),
                    // el('span', { className: 'wb-editor-badge' },
                    //     el('span', { className: 'wb-editor-badge-icon' }, '🖼️'),
                    //     __('Foto ' + roleLabel, 'weddingblocks')
                    // ),
                    el('figure', { className: 'atomic-photo shape-' + shape + (showFrame ? ' has-frame' : ' no-frame'), style: imgStyle },
                        el('img', { src: displayPhoto, alt: name, className: photo ? '' : 'atomic-photo-placeholder' })
                    )
                )
            ];
        },
        save: function () { return null; }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

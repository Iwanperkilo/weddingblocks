(function (blocks, element, blockEditor, i18n, components) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var ToggleControl = components.ToggleControl;
    var SelectControl = components.SelectControl;
    var __ = i18n.__;

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
                    )
                ),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '👥'),
                    __('Profil Pengantin', 'weddingblocks')
                ),
                el('div', { className: 'weddingblocks-couple-columns ' + layoutClass },
                    el('div', { className: 'weddingblocks-couple-column' },
                        el('div', { className: 'weddingblocks-avatar' },
                            el('img', { src: firstPhoto, alt: firstName })
                        ),
                        el('h3', {}, firstName),
                        el('p', {}, firstParents)
                    ),
                    el('div', { className: 'weddingblocks-couple-column weddingblocks-separator-column' },
                        el('p', { className: 'weddingblocks-ampersand' }, '&')
                    ),
                    el('div', { className: 'weddingblocks-couple-column' },
                        el('div', { className: 'weddingblocks-avatar' },
                            el('img', { src: secondPhoto, alt: secondName })
                        ),
                        el('h3', {}, secondName),
                        el('p', {}, secondParents)
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n, window.wp.components);
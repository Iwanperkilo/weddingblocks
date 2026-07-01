(function (blocks, element, blockEditor, i18n) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/couple-info', {
        edit: function (props) {
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

            return el('div', useBlockProps({ className: 'weddingblocks-couple-info-editor' }),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '👥'),
                    __('Profil Pengantin', 'weddingblocks')
                ),
                el('div', { className: 'weddingblocks-couple-columns' },
                    el('div', { className: 'weddingblocks-couple-column' },
                        el('div', { className: 'weddingblocks-avatar' },
                            el('img', { src: bridePhoto, alt: brideName })
                        ),
                        el('h3', {}, brideName),
                        el('p', {}, brideParents)
                    ),
                    el('div', { className: 'weddingblocks-couple-column weddingblocks-separator-column' },
                        el('p', { className: 'weddingblocks-ampersand' }, '&')
                    ),
                    el('div', { className: 'weddingblocks-couple-column' },
                        el('div', { className: 'weddingblocks-avatar' },
                            el('img', { src: groomPhoto, alt: groomName })
                        ),
                        el('h3', {}, groomName),
                        el('p', {}, groomParents)
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n);

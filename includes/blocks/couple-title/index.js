(function (blocks, element, blockEditor, i18n) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/couple-title', {
        edit: function (props) {
            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });

            var groomNickname = meta.weddingblocks_groom_nickname;
            var groomName = meta.weddingblocks_groom_name;
            var groomDisplay = groomNickname || groomName || __('Mempelai Pria', 'weddingblocks');

            var brideNickname = meta.weddingblocks_bride_nickname;
            var brideName = meta.weddingblocks_bride_name;
            var brideDisplay = brideNickname || brideName || __('Mempelai Wanita', 'weddingblocks');

            return el('div', useBlockProps({ className: 'weddingblocks-couple-title-editor' }),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '👑'),
                    __('Nama Pengantin', 'weddingblocks')
                ),
                el('h2', { className: 'weddingblocks-couple-title-text' },
                    groomDisplay + ' & ' + brideDisplay
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n);

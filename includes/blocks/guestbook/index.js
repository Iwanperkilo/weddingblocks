(function (blocks, element, blockEditor, i18n) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/guestbook', {
        edit: function (props) {
            return el('div', useBlockProps({ className: 'weddingblocks-guestbook-editor' }),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '💬'),
                    __('Buku Tamu / Daftar Ucapan', 'weddingblocks')
                ),
                el('div', { className: 'weddingblocks-guestbook-container' },
                    el('div', { className: 'guestbook-list' },
                        el('div', { className: 'guestbook-item' },
                            el('div', { className: 'guestbook-header' },
                                el('h5', { className: 'guest-name' }, __('John Doe', 'weddingblocks')),
                                el('span', { className: 'guest-status badge-hadir' }, __('Hadir', 'weddingblocks'))
                            ),
                            el('p', { className: 'guest-message' }, __('Selamat menempuh hidup baru! Semoga sakinah mawaddah warahmah.', 'weddingblocks')),
                            el('span', { className: 'guest-time' }, __('5 menit yang lalu', 'weddingblocks'))
                        )
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n);

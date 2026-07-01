(function (blocks, element, blockEditor, i18n) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/countdown', {
        edit: function (props) {
            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });

            var weddingDate = meta.weddingblocks_wedding_date || '2026-12-31T09:00';

            return el('div', useBlockProps({ key: 'editor-preview', className: 'weddingblocks-countdown-editor' }),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '⏳'),
                    __('Countdown', 'weddingblocks')
                ),
                el('p', { style: { fontSize: '12px', color: '#888', textAlign: 'center', margin: '0 0 8px' } },
                    '📅 ' + weddingDate
                ),
                el('div', { className: 'weddingblocks-countdown' },
                    el('div', { className: 'countdown-item' },
                        el('span', { className: 'countdown-value' }, '00'),
                        el('span', { className: 'countdown-label' }, 'Hari')
                    ),
                    el('div', { className: 'countdown-item' },
                        el('span', { className: 'countdown-value' }, '00'),
                        el('span', { className: 'countdown-label' }, 'Jam')
                    ),
                    el('div', { className: 'countdown-item' },
                        el('span', { className: 'countdown-value' }, '00'),
                        el('span', { className: 'countdown-label' }, 'Menit')
                    ),
                    el('div', { className: 'countdown-item' },
                        el('span', { className: 'countdown-value' }, '00'),
                        el('span', { className: 'countdown-label' }, 'Detik')
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n);

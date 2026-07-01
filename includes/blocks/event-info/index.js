(function (blocks, element, blockEditor, i18n) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/event-info', {
        edit: function (props) {
            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });

            var akadTime = meta.weddingblocks_akad_time_label || 'Pukul 08:00 - 10:00 WIB';
            var akadLocName = meta.weddingblocks_akad_location_name || 'Masjid Agung Kota';
            var akadLocAddr = meta.weddingblocks_akad_location_address || 'Jl. Cempaka No. 12';

            var resepsiTime = meta.weddingblocks_resepsi_time_label || 'Pukul 11:00 - Selesai';
            var resepsiLocName = meta.weddingblocks_resepsi_location_name || 'Gedung Serbaguna Indah';
            var resepsiLocAddr = meta.weddingblocks_resepsi_location_address || 'Jl. Melati No. 45';

            return el('div', useBlockProps({ className: 'weddingblocks-event-info-editor' }),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '📅'),
                    __('Info Acara (Akad & Resepsi)', 'weddingblocks')
                ),
                el('div', { className: 'weddingblocks-event-columns' },
                    el('div', { className: 'weddingblocks-event-card' },
                        el('h3', {}, __('Akad Nikah', 'weddingblocks')),
                        el('p', {},
                            el('strong', {}, akadTime),
                            el('br'),
                            akadLocName,
                            el('br'),
                            akadLocAddr
                        )
                    ),
                    el('div', { className: 'weddingblocks-event-card' },
                        el('h3', {}, __('Resepsi', 'weddingblocks')),
                        el('p', {},
                            el('strong', {}, resepsiTime),
                            el('br'),
                            resepsiLocName,
                            el('br'),
                            resepsiLocAddr
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

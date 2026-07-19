(function (blocks, element, blockEditor, i18n, components) {
    var el = element.createElement;
    var Fragment = element.Fragment;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelColorSettings = blockEditor.PanelColorSettings;
    var __ = i18n.__;

    var PanelBody = components.PanelBody;
    var RangeControl = components.RangeControl;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;
    var TextControl = components.TextControl;

    blocks.registerBlockType('weddingblocks/guestbook', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var entriesToShow = attributes.entriesToShow;
            var order = attributes.order;
            var showTimestamp = attributes.showTimestamp;
            var showStatusBadge = attributes.showStatusBadge;
            var emptyStateText = attributes.emptyStateText;
            var messageMaxLength = attributes.messageMaxLength;
            var cardBackgroundColor = attributes.cardBackgroundColor;
            var cardBorderColor = attributes.cardBorderColor;
            var animPanel = typeof window.weddingblocksAnimationPanel === 'function'
                ? window.weddingblocksAnimationPanel(attributes, setAttributes)
                : null;

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Pengaturan Daftar Ucapan', 'weddingblocks'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Jumlah Ucapan Ditampilkan', 'weddingblocks'),
                            value: entriesToShow,
                            onChange: function (value) { setAttributes({ entriesToShow: value }); },
                            min: 1,
                            max: 200
                        }),
                        el(SelectControl, {
                            label: __('Urutan', 'weddingblocks'),
                            value: order,
                            options: [
                                { label: __('Terbaru', 'weddingblocks'), value: 'newest' },
                                { label: __('Terlama', 'weddingblocks'), value: 'oldest' }
                            ],
                            onChange: function (value) { setAttributes({ order: value }); }
                        }),
                        el(RangeControl, {
                            label: __('Panjang Pesan (0 = tanpa batas)', 'weddingblocks'),
                            value: messageMaxLength,
                            onChange: function (value) { setAttributes({ messageMaxLength: value }); },
                            min: 0,
                            max: 500
                        })
                    ),
                    el(PanelColorSettings, {
                        title: __('Warna Kartu Ucapan', 'weddingblocks'),
                        initialOpen: false,
                        colorSettings: [
                            {
                                value: cardBackgroundColor,
                                onChange: function (value) { setAttributes({ cardBackgroundColor: value || '' }); },
                                label: __('Background', 'weddingblocks')
                            },
                            {
                                value: cardBorderColor,
                                onChange: function (value) { setAttributes({ cardBorderColor: value || '' }); },
                                label: __('Border', 'weddingblocks')
                            }
                        ]
                    }),
                    el(PanelBody, { title: __('Tampilan Elemen', 'weddingblocks'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Tampilkan Badge Status Kehadiran', 'weddingblocks'),
                            checked: showStatusBadge,
                            onChange: function (value) { setAttributes({ showStatusBadge: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Tampilkan Waktu Ucapan', 'weddingblocks'),
                            checked: showTimestamp,
                            onChange: function (value) { setAttributes({ showTimestamp: value }); }
                        }),
                        el(TextControl, {
                            label: __('Teks Saat Belum Ada Ucapan', 'weddingblocks'),
                            value: emptyStateText,
                            placeholder: __('Belum ada ucapan. Jadilah yang pertama memberikan doa restu!', 'weddingblocks'),
                            onChange: function (value) { setAttributes({ emptyStateText: value }); }
                        })
                    )
                ),
                animPanel,
                el('div', useBlockProps({ className: 'weddingblocks-guestbook-editor' }),
                    el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                        el('span', { className: 'wb-editor-badge-icon' }, '💬'),
                        __('Buku Tamu / Daftar Ucapan', 'weddingblocks')
                    ),
                    el('div', { className: 'weddingblocks-guestbook-container' },
                        el('div', { className: 'guestbook-list' },
                            el('div', {
                                className: 'guestbook-item',
                                style: {
                                    backgroundColor: cardBackgroundColor || undefined,
                                    borderColor: cardBorderColor || undefined
                                }
                            },
                                el('div', { className: 'guestbook-header' },
                                    el('h5', { className: 'guest-name' }, __('John Doe', 'weddingblocks')),
                                    showStatusBadge && el('span', { className: 'guest-status badge-hadir' }, __('Hadir', 'weddingblocks'))
                                ),
                                el('p', { className: 'guest-message' }, __('Selamat menempuh hidup baru! Semoga sakinah mawaddah warahmah.', 'weddingblocks')),
                                showTimestamp && el('span', { className: 'guest-time' }, __('5 menit yang lalu', 'weddingblocks'))
                            )
                        )
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n, window.wp.components);

(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelColorSettings = blockEditor.PanelColorSettings;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var SelectControl = components.SelectControl;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/music-player', {
        edit: function (props) {
            var attributes = props.attributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Pengaturan Musik', 'weddingblocks'), initialOpen: true },
                        el(TextControl, {
                            label: __('URL Lagu Latar (MP3)', 'weddingblocks'),
                            value: attributes.musicUrl,
                            onChange: function (value) {
                                props.setAttributes({ musicUrl: value });
                            },
                            help: __('Masukkan URL file audio MP3 (unggah lewat Media Library). Jika dikosongkan, pemutar musik tidak akan ditampilkan.', 'weddingblocks')
                        }),
                        el(ToggleControl, {
                            label: __('Ulangi Lagu (Loop)', 'weddingblocks'),
                            checked: attributes.loopMusic,
                            onChange: function (value) {
                                props.setAttributes({ loopMusic: value });
                            },
                            help: __('Jika aktif, lagu akan diputar ulang otomatis setelah selesai.', 'weddingblocks')
                        }),
                        el(SelectControl, {
                            label: __('Posisi Tombol', 'weddingblocks'),
                            value: attributes.buttonPosition,
                            options: [
                                { label: __('Kanan Bawah', 'weddingblocks'), value: 'right' },
                                { label: __('Kiri Bawah', 'weddingblocks'), value: 'left' }
                            ],
                            onChange: function (value) {
                                props.setAttributes({ buttonPosition: value });
                            }
                        })
                    ),
                    el(PanelColorSettings, {
                        title: __('Warna Tombol', 'weddingblocks'),
                        initialOpen: false,
                        enableAlpha: true,
                        colorSettings: [
                            {
                                value: attributes.buttonColor,
                                onChange: function (value) {
                                    props.setAttributes({ buttonColor: value || '#b5a46d' });
                                },
                                label: __('Warna Ikon', 'weddingblocks')
                            },
                            {
                                value: attributes.buttonBgColor,
                                onChange: function (value) {
                                    props.setAttributes({ buttonBgColor: value || 'rgba(255, 255, 255, 0.85)' });
                                },
                                label: __('Warna Background', 'weddingblocks')
                            }
                        ]
                    })
                ),
                el('div', useBlockProps({ key: 'editor-preview', className: 'weddingblocks-music-preview' }),
                    el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                        el('span', { className: 'wb-editor-badge-icon' }, '🎵'),
                        __('Music Player', 'weddingblocks')
                    ),
                    el('p', { className: 'wb-music-title' }, attributes.musicUrl ? 'Lagu Aktif' : 'Belum Ada Lagu'),
                    el('p', { className: 'wb-music-sub' },
                        attributes.musicUrl ? 'Audio URL telah diatur' : 'Isi URL lagu di panel Pengaturan Musik agar player tampil di frontend'
                    )
                )
            ];
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);
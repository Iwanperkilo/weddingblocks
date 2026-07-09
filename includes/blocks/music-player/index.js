(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
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
                        })
                    )
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
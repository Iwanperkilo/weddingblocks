/**
 * Gutenberg Document Setting Panel for WeddingBlocks.
 * Written in ES5 to eliminate compilation/build steps.
 */

/**
 * Shared Animation Panel Helper.
 * Dipanggil dari index.js setiap block via window.weddingblocksAnimationPanel().
 * Mengembalikan elemen InspectorControls PanelBody siap pakai.
 */
(function (element, blockEditor, components, i18n) {
    var el = element.createElement;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var RangeControl = components.RangeControl;
    var InspectorControls = blockEditor.InspectorControls;
    var __ = i18n.__;

    window.weddingblocksAnimationPanel = function (attributes, setAttributes) {
        var animationStyle    = attributes.animationStyle    !== undefined ? attributes.animationStyle    : 'fadeUp';
        var animationDuration = attributes.animationDuration !== undefined ? attributes.animationDuration : 600;
        var animationDelay    = attributes.animationDelay    !== undefined ? attributes.animationDelay    : 0;

        return el(
            InspectorControls,
            { key: 'wb-anim-panel', group: 'styles' },
            el(
                PanelBody,
                {
                    title: __('Animasi', 'weddingblocks'),
                    initialOpen: false,
                    className: 'wb-animation-panel'
                },
                el(SelectControl, {
                    label: __('Jenis Animasi', 'weddingblocks'),
                    value: animationStyle,
                    options: [
                        { label: __('Tanpa Animasi', 'weddingblocks'),      value: 'none'       },
                        { label: __('Fade Up (Naik)',  'weddingblocks'),     value: 'fadeUp'     },
                        { label: __('Fade In (Muncul)', 'weddingblocks'),    value: 'fadeIn'     },
                        { label: __('Slide dari Kiri', 'weddingblocks'),     value: 'slideLeft'  },
                        { label: __('Slide dari Kanan', 'weddingblocks'),    value: 'slideRight' },
                        { label: __('Zoom In (Membesar)', 'weddingblocks'),  value: 'zoomIn'     }
                    ],
                    onChange: function (value) {
                        setAttributes({ animationStyle: value });
                    }
                }),
                animationStyle !== 'none' && el(RangeControl, {
                    label: __('Durasi (ms)', 'weddingblocks'),
                    value: animationDuration,
                    min: 200,
                    max: 1500,
                    step: 100,
                    onChange: function (value) {
                        setAttributes({ animationDuration: value !== undefined ? value : 600 });
                    }
                }),
                animationStyle !== 'none' && el(RangeControl, {
                    label: __('Tunda / Delay (ms)', 'weddingblocks'),
                    value: animationDelay,
                    min: 0,
                    max: 1000,
                    step: 100,
                    help: __('Berguna untuk efek stagger antar block.', 'weddingblocks'),
                    onChange: function (value) {
                        setAttributes({ animationDelay: value !== undefined ? value : 0 });
                    }
                })
            )
        );
    };

})(window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

(function (element, blockEditor, components, i18n, editPost, plugins, data) {
    var el = element.createElement;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var MediaUpload = blockEditor.MediaUpload;
    var Button = components.Button;
    var __ = i18n.__;
    var PluginDocumentSettingPanel = editPost.PluginDocumentSettingPanel;
    var registerPlugin = plugins.registerPlugin;

    function WeddingBlocksDocumentPanel() {
        function spacedField(control) {
            return el('div', { style: { marginBottom: '16px' } }, control);
        }

        var meta = data.useSelect(function (select) {
            var editor = select('core/editor');
            if (!editor || typeof editor.getEditedPostAttribute !== 'function') {
                return {};
            }
            return editor.getEditedPostAttribute('meta') || {};
        });

        var editorDispatch = data.useDispatch('core/editor');
        var editPostAction = editorDispatch && editorDispatch.editPost ? editorDispatch.editPost : function () {};

        function updateMeta(key, value) {
            var newMeta = {};
            newMeta[key] = value;
            editPostAction({ meta: newMeta });
        }

        var postType = data.useSelect(function (select) {
            var editor = select('core/editor');
            if (!editor || typeof editor.getCurrentPostType !== 'function') {
                return null;
            }
            return editor.getCurrentPostType();
        });

        if (postType !== 'wdbl_undangan') {
            return null;
        }

        return el(PluginDocumentSettingPanel, {
            name: 'weddingblocks-data-undangan',
            title: __('Data Undangan (WeddingBlocks)', 'weddingblocks'),
            icon: 'heart',
            initialOpen: true
        },
            // Mempelai Pria Section
            el('div', { style: { marginBottom: '20px', borderBottom: '1px solid #eee', paddingBottom: '15px' } },
                el('h4', { style: { marginTop: 0, marginBottom: '12px', color: '#b5a46d' } }, __('Mempelai Pria', 'weddingblocks')),
                spacedField(el(TextControl, {
                    label: __('Nama Lengkap Pria', 'weddingblocks'),
                    value: meta.weddingblocks_groom_name || '',
                    onChange: function (val) { updateMeta('weddingblocks_groom_name', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Nama Panggilan Pria', 'weddingblocks'),
                    value: meta.weddingblocks_groom_nickname || '',
                    onChange: function (val) { updateMeta('weddingblocks_groom_nickname', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Nama Orang Tua Pria', 'weddingblocks'),
                    value: meta.weddingblocks_groom_parents || '',
                    onChange: function (val) { updateMeta('weddingblocks_groom_parents', val); },
                    help: __('Contoh: Bapak A & Ibu B', 'weddingblocks')
                })),
                el('p', { style: { margin: '10px 0 5px', fontWeight: '600', fontSize: '13px' } }, __('Foto Mempelai Pria:', 'weddingblocks')),
                el(MediaUpload, {
                    onSelect: function (media) { updateMeta('weddingblocks_groom_photo', media.url); },
                    allowedTypes: ['image'],
                    value: meta.weddingblocks_groom_photo || '',
                    render: function (obj) {
                        return el('div', {},
                            meta.weddingblocks_groom_photo && el('img', {
                                src: meta.weddingblocks_groom_photo,
                                style: { display: 'block', maxWidth: '100px', maxHeight: '100px', marginBottom: '8px', borderRadius: '4px', objectFit: 'cover' }
                            }),
                            el(Button, { isSecondary: true, onClick: obj.open }, !meta.weddingblocks_groom_photo ? __('Pilih Foto', 'weddingblocks') : __('Ganti Foto', 'weddingblocks')),
                            meta.weddingblocks_groom_photo && el(Button, {
                                isDestructive: true,
                                isLink: true,
                                onClick: function () { updateMeta('weddingblocks_groom_photo', ''); },
                                style: { marginLeft: '10px' }
                            }, __('Hapus', 'weddingblocks'))
                        );
                    }
                })
            ),

            // Mempelai Wanita Section
            el('div', { style: { marginBottom: '20px', borderBottom: '1px solid #eee', paddingBottom: '15px' } },
                el('h4', { style: { marginTop: 0, marginBottom: '12px', color: '#b5a46d' } }, __('Mempelai Wanita', 'weddingblocks')),
                spacedField(el(TextControl, {
                    label: __('Nama Lengkap Wanita', 'weddingblocks'),
                    value: meta.weddingblocks_bride_name || '',
                    onChange: function (val) { updateMeta('weddingblocks_bride_name', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Nama Panggilan Wanita', 'weddingblocks'),
                    value: meta.weddingblocks_bride_nickname || '',
                    onChange: function (val) { updateMeta('weddingblocks_bride_nickname', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Nama Orang Tua Wanita', 'weddingblocks'),
                    value: meta.weddingblocks_bride_parents || '',
                    onChange: function (val) { updateMeta('weddingblocks_bride_parents', val); },
                    help: __('Contoh: Bapak C & Ibu D', 'weddingblocks')
                })),
                el('p', { style: { margin: '10px 0 5px', fontWeight: '600', fontSize: '13px' } }, __('Foto Mempelai Wanita:', 'weddingblocks')),
                el(MediaUpload, {
                    onSelect: function (media) { updateMeta('weddingblocks_bride_photo', media.url); },
                    allowedTypes: ['image'],
                    value: meta.weddingblocks_bride_photo || '',
                    render: function (obj) {
                        return el('div', {},
                            meta.weddingblocks_bride_photo && el('img', {
                                src: meta.weddingblocks_bride_photo,
                                style: { display: 'block', maxWidth: '100px', maxHeight: '100px', marginBottom: '8px', borderRadius: '4px', objectFit: 'cover' }
                            }),
                            el(Button, { isSecondary: true, onClick: obj.open }, !meta.weddingblocks_bride_photo ? __('Pilih Foto', 'weddingblocks') : __('Ganti Foto', 'weddingblocks')),
                            meta.weddingblocks_bride_photo && el(Button, {
                                isDestructive: true,
                                isLink: true,
                                onClick: function () { updateMeta('weddingblocks_bride_photo', ''); },
                                style: { marginLeft: '10px' }
                            }, __('Hapus', 'weddingblocks'))
                        );
                    }
                })
            ),

            // Jadwal & Kontak Section
            el('div', {},
                el('h4', { style: { marginTop: 0, marginBottom: '12px', color: '#b5a46d' } }, __('Jadwal & Kontak', 'weddingblocks')),
                spacedField(el(TextControl, {
                    label: __('Tanggal & Jam Pernikahan', 'weddingblocks'),
                    value: meta.weddingblocks_wedding_date || '',
                    placeholder: 'YYYY-MM-DDTHH:MM',
                    onChange: function (val) { updateMeta('weddingblocks_wedding_date', val); },
                    help: __('Contoh: 2026-12-25T08:00')
                })),
                spacedField(el(TextControl, {
                    label: __('Waktu Akad (Teks)', 'weddingblocks'),
                    value: meta.weddingblocks_akad_time_label || '',
                    placeholder: 'Pukul 08:00 - 10:00 WIB',
                    onChange: function (val) { updateMeta('weddingblocks_akad_time_label', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Tempat Akad', 'weddingblocks'),
                    value: meta.weddingblocks_akad_location_name || '',
                    placeholder: 'Masjid Agung Kota',
                    onChange: function (val) { updateMeta('weddingblocks_akad_location_name', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Alamat Akad', 'weddingblocks'),
                    value: meta.weddingblocks_akad_location_address || '',
                    placeholder: 'Jl. Cempaka No. 12',
                    onChange: function (val) { updateMeta('weddingblocks_akad_location_address', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Waktu Resepsi (Teks)', 'weddingblocks'),
                    value: meta.weddingblocks_resepsi_time_label || '',
                    placeholder: 'Pukul 11:00 - Selesai',
                    onChange: function (val) { updateMeta('weddingblocks_resepsi_time_label', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Tempat Resepsi', 'weddingblocks'),
                    value: meta.weddingblocks_resepsi_location_name || '',
                    placeholder: 'Gedung Serbaguna Indah',
                    onChange: function (val) { updateMeta('weddingblocks_resepsi_location_name', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Alamat Resepsi', 'weddingblocks'),
                    value: meta.weddingblocks_resepsi_location_address || '',
                    placeholder: 'Jl. Melati No. 45',
                    onChange: function (val) { updateMeta('weddingblocks_resepsi_location_address', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('Tautan Google Maps', 'weddingblocks'),
                    value: meta.weddingblocks_maps_coords || '',
                    placeholder: 'https://maps.google.com/?q=...',
                    onChange: function (val) { updateMeta('weddingblocks_maps_coords', val); }
                })),
                spacedField(el(TextControl, {
                    label: __('No. WhatsApp Admin', 'weddingblocks'),
                    value: meta.weddingblocks_whatsapp_number || '',
                    placeholder: '628123456789',
                    onChange: function (val) { updateMeta('weddingblocks_whatsapp_number', val); }
                }))
            )
        );
    }

    registerPlugin('weddingblocks-document-settings', {
        render: WeddingBlocksDocumentPanel
    });

})(window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n, window.wp.editPost, window.wp.plugins, window.wp.data);

/**
 * Register filters to extend Core Blocks (Heading, Paragraph, Group, Image, Button, Buttons)
 * with the animation settings panel.
 */
(function (hooks, compose, element) {
    if (!hooks || !compose || !element) {
        return;
    }

    var el = element.createElement;
    var allowedBlocks = [
        'core/paragraph',
        'core/heading',
        'core/image',
        'core/group',
        'core/buttons',
        'core/button'
    ];

    // 1. Add attributes to core blocks
    function addAnimAttributes(settings, name) {
        if (allowedBlocks.indexOf(name) !== -1) {
            settings.attributes = Object.assign({}, settings.attributes, {
                animationStyle: {
                    type: 'string',
                    default: 'none'
                },
                animationDuration: {
                    type: 'number',
                    default: 600
                },
                animationDelay: {
                    type: 'number',
                    default: 0
                }
            });
        }
        return settings;
    }
    hooks.addFilter('blocks.registerBlockType', 'weddingblocks/add-core-anim-attrs', addAnimAttributes);

    // 2. Inject Animation Setting Panel in Gutenberg Sidebar
    var withInspectorControls = compose.createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (allowedBlocks.indexOf(props.name) !== -1 && props.attributes) {
                var animPanel = typeof window.weddingblocksAnimationPanel === 'function'
                    ? window.weddingblocksAnimationPanel(props.attributes, props.setAttributes)
                    : null;

                return el(element.Fragment, {},
                    el(BlockEdit, props),
                    animPanel
                );
            }
            return el(BlockEdit, props);
        };
    }, 'withInspectorControls');
    hooks.addFilter('editor.BlockEdit', 'weddingblocks/add-core-anim-inspector', withInspectorControls);

})(window.wp.hooks, window.wp.compose, window.wp.element);

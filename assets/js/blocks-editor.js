/**
 * Gutenberg Document Setting Panel for WeddingBlocks.
 * Written in ES5 to eliminate compilation/build steps.
 */
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
            return select('core/editor').getEditedPostAttribute('meta') || {};
        });

        var editPostAction = data.useDispatch('core/editor').editPost;

        function updateMeta(key, value) {
            var newMeta = {};
            newMeta[key] = value;
            editPostAction({ meta: newMeta });
        }

        var postType = data.useSelect(function (select) {
            return select('core/editor').getCurrentPostType();
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
(function (blocks, element, blockEditor, i18n, components) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var __ = i18n.__;

    blocks.registerBlockType('weddingblocks/event-info', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            });

            var akadTime = meta.weddingblocks_akad_time_label || 'Pukul 08:00 - 10:00 WIB';
            var akadLocName = meta.weddingblocks_akad_location_name || 'Masjid Agung Kota';
            var akadLocAddr = meta.weddingblocks_akad_location_address || 'Jl. Cempaka No. 12';

            var resepsiTime = meta.weddingblocks_resepsi_time_label || 'Pukul 11:00 - Selesai';
            var resepsiLocName = meta.weddingblocks_resepsi_location_name || 'Gedung Serbaguna Indah';
            var resepsiLocAddr = meta.weddingblocks_resepsi_location_address || 'Jl. Melati No. 45';

            var layoutVariation = attributes.layoutVariation || 'horizontal';
            var wrapperClassName = 'weddingblocks-event-info-editor weddingblocks-event-info-editor--' + layoutVariation;
            var events = [
                {
                    title: __('Akad Nikah', 'weddingblocks'),
                    time: akadTime,
                    name: akadLocName,
                    addr: akadLocAddr
                },
                {
                    title: __('Resepsi', 'weddingblocks'),
                    time: resepsiTime,
                    name: resepsiLocName,
                    addr: resepsiLocAddr
                }
            ];

            function renderEventCard(event) {
                return el('div', { className: 'weddingblocks-event-card' },
                    el('h3', {}, event.title),
                    el('p', {},
                        el('strong', {}, event.time),
                        el('span', { className: 'weddingblocks-event-card__meta' }, event.name),
                        el('span', { className: 'weddingblocks-event-card__meta' }, event.addr)
                    )
                );
            }

            return el('div', useBlockProps({ className: wrapperClassName }),
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Variasi Tata Letak', 'weddingblocks'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Pilih Layout', 'weddingblocks'),
                            value: layoutVariation,
                            options: [
                                { value: 'vertical', label: __('Vertical Stack (Mobile)', 'weddingblocks') },
                                { value: 'horizontal', label: __('Horizontal Split (Desktop)', 'weddingblocks') },
                                { value: 'timeline', label: __('Timeline View', 'weddingblocks') }
                            ],
                            onChange: function (value) {
                                setAttributes({ layoutVariation: value });
                            }
                        })
                    )
                ),
                el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                    el('span', { className: 'wb-editor-badge-icon' }, '📅'),
                    __('Info Acara (Akad & Resepsi)', 'weddingblocks')
                ),
                layoutVariation === 'timeline'
                    ? el('div', { className: 'weddingblocks-event-columns weddingblocks-event-columns--timeline' },
                        el('div', { className: 'weddingblocks-timeline' },
                            events.map(function (event, index) {
                                return el('div', {
                                    key: event.title,
                                    className: 'weddingblocks-timeline-item ' + (index === 0 ? 'weddingblocks-timeline-item--start' : 'weddingblocks-timeline-item--end')
                                },
                                    el('span', { className: 'weddingblocks-timeline-marker', 'aria-hidden': 'true' }),
                                    el('div', { className: 'weddingblocks-timeline-content' }, renderEventCard(event))
                                );
                            })
                        )
                    )
                    : el('div', { className: 'weddingblocks-event-columns weddingblocks-event-columns--' + layoutVariation },
                        events.map(function (event) {
                            return renderEventCard(event);
                        })
                    )
            );
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.i18n, window.wp.components);

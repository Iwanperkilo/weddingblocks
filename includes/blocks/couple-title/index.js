(function (blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var ColorPalette = components.ColorPalette;
    var useBlockProps = blockEditor.useBlockProps;
    var __ = i18n.__;

    var colorPalette = [
        { name: __('White', 'weddingblocks'), color: '#ffffff' },
        { name: __('Gold', 'weddingblocks'), color: '#b5a46d' },
        { name: __('Dark Gold', 'weddingblocks'), color: '#8a7a4f' },
        { name: __('Rose', 'weddingblocks'), color: '#b76e79' },
        { name: __('Maroon', 'weddingblocks'), color: '#800000' },
        { name: __('Navy', 'weddingblocks'), color: '#1f3a5f' },
        { name: __('Charcoal', 'weddingblocks'), color: '#2c2c2c' }
    ];

    blocks.registerBlockType('weddingblocks/couple-title', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var meta = wp.data.useSelect(function (select) {
                return select('core/editor').getEditedPostAttribute('meta') || {};
            }, []);

            var groomName = attributes.groomName || meta.weddingblocks_groom_name || '';
            var groomNickname = attributes.groomNickname || meta.weddingblocks_groom_nickname || '';
            var brideName = attributes.brideName || meta.weddingblocks_bride_name || '';
            var brideNickname = attributes.brideNickname || meta.weddingblocks_bride_nickname || '';

            var groomDisplay = groomNickname || groomName || __('Mempelai Pria', 'weddingblocks');
            var brideDisplay = brideNickname || brideName || __('Mempelai Wanita', 'weddingblocks');

            var textColor = attributes.textColor || '#ffffff';
            var textTransform = attributes.textTransform || 'none';
            var separator = attributes.separator || '&';

            var transformText = function (text) {
                if (textTransform === 'uppercase') {
                    return text.toUpperCase();
                }
                if (textTransform === 'lowercase') {
                    return text.toLowerCase();
                }
                if (textTransform === 'capitalize') {
                    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
                }
                return text;
            };

            var titleStyle = {
                color: textColor,
                textTransform: textTransform
            };

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, {
                        title: __('Pengaturan Nama Cover', 'weddingblocks'),
                        initialOpen: true
                    },
                        el('p', { style: { marginBottom: '8px', fontWeight: '500' } }, __('Warna Teks', 'weddingblocks')),
                        el(ColorPalette, {
                            value: textColor,
                            colors: colorPalette,
                            onChange: function (value) {
                                setAttributes({ textColor: value || '#ffffff' });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Transformasi Teks', 'weddingblocks'),
                            value: textTransform,
                            options: [
                                { label: __('None', 'weddingblocks'), value: 'none' },
                                { label: __('Uppercase', 'weddingblocks'), value: 'uppercase' },
                                { label: __('Lowercase', 'weddingblocks'), value: 'lowercase' },
                                { label: __('Capitalize', 'weddingblocks'), value: 'capitalize' }
                            ],
                            onChange: function (value) {
                                setAttributes({ textTransform: value });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Tanda Hubung', 'weddingblocks'),
                            value: separator,
                            options: [
                                { label: '&', value: '&' },
                                { label: '\u2764\ufe0f', value: '\u2764\ufe0f' },
                                { label: '\u272d\ufe0f', value: '\u272d\ufe0f' },
                                { label: '\u2014', value: '\u2014' }
                            ],
                            onChange: function (value) {
                                setAttributes({ separator: value });
                            }
                        })
                    )
                ),
                el('div', useBlockProps({ key: 'preview' }),
                    el('span', { className: 'wb-editor-badge wb-editor-badge--block' },
                        el('span', { className: 'wb-editor-badge-icon' }, '\ud83d\udc51'),
                        __('Nama Pengantin', 'weddingblocks')
                    ),
                    el('h2', {
                        className: 'weddingblocks-couple-title-text',
                        style: titleStyle
                    }, transformText(groomDisplay) + ' ' + separator + ' ' + transformText(brideDisplay))
                )
            ];
        },
        save: function () {
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

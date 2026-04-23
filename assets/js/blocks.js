(function (wp) {
    if (!wp || !wp.blocks) {
        return;
    }

    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var registerBlockType = wp.blocks.registerBlockType;
    var ServerSideRender = wp.serverSideRender;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var __ = wp.i18n.__;

    function preview(name, attributes) {
        return el(ServerSideRender, {
            block: name,
            attributes: attributes,
            EmptyResponsePlaceholder: function () {
                return el('div', {
                    style: {
                        padding: '16px',
                        background: '#f0f0f1',
                        border: '1px dashed #c3c4c7',
                        borderRadius: '4px',
                        color: '#50575e',
                        fontStyle: 'italic'
                    }
                }, __('Geen voorbeeld: vul een Cursus ID in de sidebar in, of plaats dit block op een pagina met ?cursus_id= in de URL.', 'mentor-integration'));
            }
        });
    }

    function idPanel(props) {
        return el(InspectorControls, {},
            el(PanelBody, { title: __('Instellingen', 'mentor-integration'), initialOpen: true },
                el(TextControl, {
                    label: __('Cursus ID (optioneel)', 'mentor-integration'),
                    help: __('Laat leeg om ?cursus_id uit de URL te gebruiken.', 'mentor-integration'),
                    value: props.attributes.id || '',
                    onChange: function (value) { props.setAttributes({ id: value }); }
                })
            )
        );
    }

    registerBlockType('mentor/courses', {
        title: __('Mentor: Cursussen', 'mentor-integration'),
        description: __('Toont de cursuscatalogus.', 'mentor-integration'),
        icon: 'welcome-learn-more',
        category: 'mentor',
        supports: { html: false },
        edit: function () { return preview('mentor/courses', {}); },
        save: function () { return null; }
    });

    registerBlockType('mentor/categories', {
        title: __('Mentor: Categorieën', 'mentor-integration'),
        description: __('Toont cursuscategorieën.', 'mentor-integration'),
        icon: 'category',
        category: 'mentor',
        supports: { html: false },
        edit: function () { return preview('mentor/categories', {}); },
        save: function () { return null; }
    });

    function withIdBlock(name, title, icon, description) {
        registerBlockType(name, {
            title: title,
            description: description,
            icon: icon,
            category: 'mentor',
            supports: { html: false },
            attributes: { id: { type: 'string', default: '' } },
            edit: function (props) {
                return el(Fragment, {},
                    idPanel(props),
                    preview(name, props.attributes)
                );
            },
            save: function () { return null; }
        });
    }

    withIdBlock(
        'mentor/trainingtracks',
        __('Mentor: Trainingstracks', 'mentor-integration'),
        'calendar-alt',
        __('Toont beschikbare trainingstracks.', 'mentor-integration')
    );
    withIdBlock(
        'mentor/startdata',
        __('Mentor: Startdata', 'mentor-integration'),
        'calendar',
        __('Toont startmomenten van een cursus.', 'mentor-integration')
    );
    withIdBlock(
        'mentor/reviews',
        __('Mentor: Reviews', 'mentor-integration'),
        'star-filled',
        __('Toont reviews van een cursus.', 'mentor-integration')
    );
    withIdBlock(
        'mentor/cursus-detail',
        __('Mentor: Cursusdetail', 'mentor-integration'),
        'welcome-write-blog',
        __('Toont de volledige cursusdetailpagina.', 'mentor-integration')
    );

    var fieldOptions = [
        { label: __('Titel', 'mentor-integration'), value: 'titel' },
        { label: __('Prijs', 'mentor-integration'), value: 'prijs' },
        { label: __('Omschrijving', 'mentor-integration'), value: 'omschrijving' },
        { label: __('Afbeelding', 'mentor-integration'), value: 'afbeelding' },
        { label: __('Thema', 'mentor-integration'), value: 'thema' },
        { label: __('Docenten', 'mentor-integration'), value: 'docenten' },
        { label: __('Inschrijfknop', 'mentor-integration'), value: 'inschrijven' },
        { label: __('Reviews', 'mentor-integration'), value: 'reviews' }
    ];

    registerBlockType('mentor/cursus-field', {
        title: __('Mentor: Cursusveld', 'mentor-integration'),
        description: __('Toont één veld van een cursus (titel, prijs, omschrijving, …).', 'mentor-integration'),
        icon: 'editor-textcolor',
        category: 'mentor',
        supports: { html: false },
        attributes: {
            id: { type: 'string', default: '' },
            field: { type: 'string', default: 'titel' }
        },
        edit: function (props) {
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Instellingen', 'mentor-integration'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Veld', 'mentor-integration'),
                            value: props.attributes.field,
                            options: fieldOptions,
                            onChange: function (value) { props.setAttributes({ field: value }); }
                        }),
                        el(TextControl, {
                            label: __('Cursus ID (optioneel)', 'mentor-integration'),
                            help: __('Laat leeg om ?cursus_id uit de URL te gebruiken.', 'mentor-integration'),
                            value: props.attributes.id || '',
                            onChange: function (value) { props.setAttributes({ id: value }); }
                        })
                    )
                ),
                preview('mentor/cursus-field', props.attributes)
            );
        },
        save: function () { return null; }
    });

})(window.wp);

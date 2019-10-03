( function( blocks, editor, i18n, element, components, _ ) {
	var el = element.createElement;
	var RichText = editor.RichText;
	var MediaUpload = editor.MediaUpload;
	var SelectControl = wp.components.SelectControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var terms = get_terms();

	function httpGetSync( theUrl, callback ) {
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.onreadystatechange = function() { 
			if ( xmlHttp.readyState == 4 && xmlHttp.status == 200 )
				callback( xmlHttp.responseText );
		}
		xmlHttp.open( "GET", theUrl, false );
		xmlHttp.send( null );
	}

	function get_terms() {
		var terms = [
			{ value: '-1', label: i18n.__( 'Все категории', 'pstu-faq' ) },
		];
		httpGetSync( 'http://localhost/plugins/wp-json/wp/v2/faq_category', function( answer ) {
			JSON.parse( answer ).forEach( function( term, index ) {
				terms[ terms.length ] = {
					value: term.id,
					label: term.name
				}
			} );
		} );
		return terms;
	}


	function set_shortcode( props, id ) {
		var shortcode = '[FAQ_THE_CATEGORY_POSTS id="' + id + '" ]';
		props.setAttributes( { shortcode: shortcode } );
	}


	blocks.registerBlockType( 'pstu-faq/the-category-posts', {
		title: i18n.__( 'Категория вопросов ответов', 'pstu-faq' ),
		description: i18n.__( '', 'pstu-faq' ),
		keywords: [
			i18n.__( 'ПГТУ', 'pstu-faq' ),
			i18n.__( 'вопросы-ответы', 'pstu-faq' ),
			i18n.__( 'список', 'pstu-faq' ),
			i18n.__( 'категория', 'pstu-faq' ),
			i18n.__( 'помощь', 'pstu-faq' ),
		],
		icon: 'sos',
		category: 'widgets',
		attributes: {
			id: {
				type: 'string',
				default: ''
			},
			shortcode: {
				type: 'array',
				source: 'children',
				selector: 'div',
				default: ''
			},
		},
		edit: function( props ) {
			var id = props.attributes.id;
			return el( 'div', { className: props.className },
				el( wp.editor.InspectorControls, null,
					el( wp.components.PanelBody,
						{
							title: i18n.__( 'Параметры шорткода', 'pstu-faq' ),
							initialOpen: true,
						},
						el( SelectControl, {
							label: i18n.__( 'Название категории', 'pstu-faq' ),
							value: props.attributes.id,
							options: terms,
							onChange: function( value ) {
								props.setAttributes( { id: value } );
								set_shortcode( props, value );
							},
						} ),
					),
				),
				( props.attributes.id.length > 0 ) ? [
					el( 'div', {}, terms.find( x=>x.value==props.attributes.id ).label ),
					el( 'code', {}, props.attributes.shortcode )
				] : el( 'div', {}, i18n.__( 'Выберите категорию', 'pstu-faq' ) ),
			);
		},

		save: function( props ) {
			return el( 'div', {}, props.attributes.shortcode );
		},

	} );

} )(
	window.wp.blocks,
	window.wp.editor,
	window.wp.i18n,
	window.wp.element,
	window.wp.components, 
	window._,
);
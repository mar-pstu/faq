( function ( $ ) {


	var param = {};


	if ( 'underfined' != typeof( faq_form_show ) ) {
		params = faq_form_show;
		jQuery( '.wp-block-pstu-faq-form-show form' ).on( 'submit', send );
	}





	function answer( mesage ) {
		jQuery.fancybox.open( mesage );
	}





	function message( $controls ) {
		var query = {};
		$controls.each( function ( index, field ) {
			query[ jQuery( field ).attr( 'name' ) ] = jQuery( field ).val();
		} );
		return query;
	}




	function send() {

		event.preventDefault();

		var $form = jQuery( this ),
			$submit = $form.find( '[type="submit"]' ),
			$controls = $form.find( 'input:not( [type=submit] ), textarea, select' );

		var data = {
			action: params.action,
			query: message( $controls ),
			security: params.security
		}

		
		jQuery.ajax( {
			type: params.method,
			url: params.ajaxurl,
			data: data,
			beforeSend: function( xhr ) {
				$submit.attr( 'disabled', 'disabled' );
			},
			success: function( data ) {
				if ( 'underfined' != typeof( data.success ) && data.success ) {
					$controls.not( '[readonly]' ).val( '' );
					answer( params.success + ' ' + data.data );
				} else {
					answer( params.error + ' ' + data.data );
				}
				$submit.removeAttr( 'disabled' );
			},
			error: function( data ) {
				answer( params.error );
				$submit.removeAttr( 'disabled' );
			}
		} );
	}



} )( jQuery );
define( 
		[
		 "jquery",
		 "http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"
		 ],
		 function( $ ) {
	"use strict"; // jshint ;_;

	/**
	 * Initialize reCAPTCHA field if it hasn't been already.
	 */
	var init_recaptcha = function( $form ) {
		var $recaptcha = $( '.ai1ec-recaptcha', $form );

		if ( $recaptcha.length === 0 ) {
			return;
		}
		if ( $recaptcha.is( '.ai1ec-initializing, .ai1ec-initialized' ) ) {
			return;
		}
		if ( typeof Recaptcha === 'undefined' ) {
			return;
		}
		Recaptcha.create(
			$recaptcha.data( 'recaptchaKey' ),
			$recaptcha[0],
			{
				theme: 'white',
				callback: function() {
					$( '#recaptcha_response_field', $recaptcha )
						.attr( 'placeholder', $recaptcha.data( 'placeholder' ) );
					$recaptcha
						.removeClass( 'ai1ec-initializing' )
						.addClass( 'ai1ec-initialized' );
				},
			}
		);
		$recaptcha.addClass( 'ai1ec-initializing' );
	};
	return {
		init_recaptcha : init_recaptcha
	};
} );
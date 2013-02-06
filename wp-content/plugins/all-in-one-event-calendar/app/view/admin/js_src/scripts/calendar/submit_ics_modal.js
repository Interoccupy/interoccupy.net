define(
	[
		"jquery",
		"ai1ec_config",
		"libs/utils",
		"libs/recaptcha",
		"libs/categories_select",
		"libs/collapse_helper",
		"external_libs/jquery.placeholder"
	],
	function( $, ai1ec_config, utils, recaptcha, categories_select ) {
	"use strict"; // jshint ;_;

	var $form = $( '.ai1ec-submit-ics-form' )
	 ,  $spinner = $( '#ai1ec-submit-ics-modal .ai1ec-loading' );

	/**
	 * Initialize the iCalendar feed submission form.
	 */
	var init_form = function() {
		recaptcha.init_recaptcha( $form );
		$( 'input, textarea', $form ).placeholder();
		categories_select.init( $form );
		$( 'input' ).placeholder();
	};

	var init_recaptcha = function() {
		recaptcha.init_recaptcha( $form );
	};

	var handle_form_submission = function( e ) {
		e.preventDefault();
		$( '.ai1ec-alerts', $form ).html( '' );
		var ics = $( '#ai1ec_calendar_url', $form ).val();
		var email = $( '#ai1ec_submitter_email', $form ).val();
		if( ics === '' || email === '' ) {
			var $alert = utils.make_alert( ai1ec_config.mail_url_required, 'error', true );
			$( '.ai1ec-alerts', $form ).append( $alert );
		}
		else {
			if( ! utils.isUrl( ics ) ) {
				var $alert = utils.make_alert( ai1ec_config.invalid_url_message, 'error', true );
				$( '.ai1ec-alerts', $form ).append( $alert );
				$( '#ai1ec_calendar_url', $form ).focus();
				return;
			}
			if( ! utils.isValidEmail( email ) ) {
				var $alert = utils.make_alert( ai1ec_config.invalid_email_message, 'error', true );
				$( '.ai1ec-alerts', $form ).append( $alert );
				$( '#ai1ec_submitter_email', $form ).focus();
				return;
			}

			var form_data = $form.serialize();
			$spinner.addClass( 'show' );
			$.ajax( {
				data: form_data + "&action=ai1ec_add_ics_frontend",
				type: 'POST',
				dataType: 'json',
				url: ai1ec_config.ajax_url,
				success: function( data ) {
					$spinner.removeClass( 'show' );
					if ( $( '#recaptcha_response_field', $form ).length &&
					     typeof Recaptcha !== 'undefined' ) {
						// Fetch new CAPTCHA challenge.
						Recaptcha.reload();
					}
					var outcome = data.success ? 'success' : 'error';
					var $alert = utils.make_alert( data.message, outcome, true );
					$( '.ai1ec-alerts', $form ).append( $alert );
					$( '.ai1ec-nonce-fields', $form ).html( data.nonce );
					if ( 'success' === outcome ) {
						$( '#ai1ec_calendar_url, #ai1ec_submitter_email', $form ).val( '' );
						$( '#ai1ec_categories', $form ).select2( "val", "" );
					}
				}
			} );
		}
	};

	return {
		handle_form_submission : handle_form_submission,
		init_form              : init_form,
		init_recaptcha         : init_recaptcha
	};

} );

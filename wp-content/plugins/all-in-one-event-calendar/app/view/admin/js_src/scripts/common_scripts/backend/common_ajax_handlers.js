define(
	[
		"jquery"
	],
	function( $ ) {
	"use strict"; // jshint ;_;

	var handle_dismiss_plugins = function( response ) {
		if ( response ) {
			if( typeof response.message !== 'undefined' ) {
				window.alert( response.message );
			} else {
				$( '.ai1ec-facebook-cron-dismiss-notification' ).closest( '.message' ).fadeOut();
			}
		}
	};

	var handle_dismiss_notification = function( response ) {
		if( response.error ) {
			// tell the user that there is an error
			window.alert( response.message );
		} else {
			// hide notification message
			$( '.ai1ec-dismiss-notification' ).closest( '.message' ).fadeOut();
		}
	};

	var handle_dismiss_intro_video = function( response ) {
		if( response.error ) {
			// Tell the user that there is an error.
			window.alert( response.message );
		} else {
			// Hide notification message.
			$( '.ai1ec-dismiss-intro-video' ).closest( '.message' ).fadeOut();
		}
	};

	/**
	 * AJAX result after clicking Dismiss in license warning.
	 * @param  {object} response Data returned by HTTP response
	 */
	var handle_dismiss_license_warning = function( response ) {
		if( response.error ) {
			// Tell the user that there is an error.
			window.alert( response.message );
		} else {
			// Hide notification message.
			$( '.ai1ec-dismiss-license-warning' ).closest( '.message' ).fadeOut();
		}
	};

	return {
		handle_dismiss_plugins        : handle_dismiss_plugins,
		handle_dismiss_notification   : handle_dismiss_notification,
		handle_dismiss_intro_video    : handle_dismiss_intro_video,
		handle_dismiss_license_warning: handle_dismiss_license_warning
	};
} );

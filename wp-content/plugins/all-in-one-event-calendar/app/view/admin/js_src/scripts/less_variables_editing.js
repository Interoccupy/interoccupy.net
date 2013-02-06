define(
	[
		"jquery",
		"domReady",
		"ai1ec_config",
		"external_libs/bootstrap_colorpicker",
		"external_libs/bootstrap_tab",
		"external_libs/jquery_cookie"
	],
	function( $, domReady, ai1ec_config ) {
	"use strict"; // jshint ;_;

	var handle_set_tab_cookie = function( e ) {
		var active = $( this ).attr( 'href' );
		$.cookie( 'less_variables_active_tab', active );
	};

	var activate_saved_tab_on_page_load = function( active_tab ) {
		if ( active_tab === null ){
			// Activate the first tab
			$( 'ul.nav-tabs a:first' ).tab( 'show' );
		} else {
			// Activate the correct tab
			$( 'ul.nav-tabs a[href=' + active_tab + ']' ).tab( 'show' );
		}
	};

	/**
	 * Ask user to confirm resetting their theme options.
	 *
	 * @return {boolean} True if should proceed with click, false otherwise
	 */
	var confirm_on_reset = function() {
		return window.confirm( ai1ec_config.confirm_reset_theme );
	};

	domReady( function() {
		$( '.colorpickers' ).colorpicker();
		activate_saved_tab_on_page_load( $.cookie( 'less_variables_active_tab' ) );
		$( 'ul.nav-tabs a' ).click( handle_set_tab_cookie );
		$( '#ai1ec-reset-variables' ).on( 'click', confirm_on_reset );
	} );
});

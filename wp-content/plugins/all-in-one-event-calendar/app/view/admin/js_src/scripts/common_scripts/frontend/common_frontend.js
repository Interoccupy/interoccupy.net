define(
	[
		"jquery",
		"domReady",
		"scripts/common_scripts/frontend/common_event_handlers",
		"ai1ec_calendar",
		"external_libs/modernizr",
		"external_libs/bootstrap_tooltip",
		"external_libs/bootstrap_popover"
	],
	function( $, domReady, event_handlers, ai1ec_calendar, Modernizr ) {
	"use strict"; // jshint ;_;

	var event_listeners_attached = false;

	var attach_event_handlers_frontend = function() {
		event_listeners_attached = true;
		$( document ).on( 'mouseenter', '.ai1ec-popup-trigger', event_handlers.handle_popover_over );
		$( document ).on( 'mouseleave', '.ai1ec-popup-trigger', event_handlers.handle_popover_out );
		$( document ).on( 'mouseleave', '.ai1ec-popup', event_handlers.handle_popover_self_out );
		$( document ).on( 'mouseenter', '.ai1ec-tooltip-trigger', event_handlers.handle_tooltip_over );
		$( document ).on( 'mouseleave', '.ai1ec-popup .ai1ec-tooltip-trigger', event_handlers.handle_tooltip_out );
		$( document ).on( 'mouseleave', '.tooltip', event_handlers.handle_tooltip_self_out );
	};

	/**
	 * Load the fonts and trigger actions to happen when fonts have loaded.
	 */
	var load_fonts = function() {
		var families = [];
		var url = [];
		$.each( ai1ec_calendar.fonts, function() {
			families.push( this.name );
			url.push( this.url );
		} );
		var data = {
			active: event_handlers.handle_fonts_loaded,
			custom : {
				families : families,
				urls : url
			}
		};
		require(
			[ 'external_libs/webfont' ],
			function() {
				WebFont.load(data);
			} );
	};

	/**
	 * Initialize page.
	 */
	var start = function() {
		load_fonts();
		domReady( function() {
			attach_event_handlers_frontend();
		} );
	};

	/**
	 * Returns whether event listeners have been attached.
	 *
	 * @return {boolean}
	 */
	var are_event_listeners_attached = function() {
		return event_listeners_attached;
	};

	return {
		start                        : start,
		are_event_listeners_attached : are_event_listeners_attached
	};
} );

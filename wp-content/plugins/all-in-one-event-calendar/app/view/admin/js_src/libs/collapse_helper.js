define(
	[
		"jquery",
		"domReady",
		"external_libs/bootstrap_collapse"
	],
	function( $, domReady ) {
	"use strict";

	domReady( function() {
		// Toggle visibility of .icon-caret-down/.icon-caret-up in collapse triggers
		// when they are clicked.
		$( document ).on( 'click', '[data-toggle="collapse"]', function() {
			$( this ).toggleClass( 'active' );
			$( '.icon-caret-down, .icon-caret-up, .icon-chevron-down, .icon-chevron-up, .icon-arrow-down, .icon-arrow-up', this )
				.toggleClass( 'hide' );
		} );
	} );
} );

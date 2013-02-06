define(
		[
		 "jquery",
		 "domReady",
		 "external_libs/bootstrap_dropdown",
		 "external_libs/bootstrap_tooltip"
		 ],
		function( $, domReady ) {
	"use strict"; // jshint ;_;
	var start = function() {
		$( document ).on( 'mouseenter', '.ai1ec-tooltip-trigger', function() {
			// Don't add tooltips to category colour squares already contained in
			// descriptive category labels.
			if( $( this ).is( '.ai1ec-category .ai1ec-category-color' ) ) {
				return;
			}
			// Only register .tooltip() the first time it is hovered.
			if( ! $( this ).data( 'tooltipped.ai1ec' ) ) {
				$( this )
					.tooltip()
					.tooltip( 'show' )
					.data( 'tooltipped.ai1ec', true );
			}
		} );
	};
	return {
		start : start
	};
} );

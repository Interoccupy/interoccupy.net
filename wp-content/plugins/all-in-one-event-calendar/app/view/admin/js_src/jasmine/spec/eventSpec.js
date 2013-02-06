require( [
         "jquery",
         'domReady',
         'scripts/event/gmaps_helper',
         'scripts/event'
         ],
         function ( $, domReady, gmaps_helper, page ) {
	"use strict"; // jshint ;_;
	// Globalize jQuery
	window.jQuery = $;
	window.$ = $;
	// Set the ajaxurl variable for testing purpose
	var ajaxurl = "http://localhost/wordpress/wp-admin/admin-ajax.php";
	afterEach(  function() {
		// Unset any handlers that are delegated to the window object. This is important otherwise every time page.start() is called all the handlers are bound one more time
		$( window ).off();
	});
	describe( "Single event Test suite", function() {
		beforeEach( function() {
			loadFixtures( 'frontend/event.html' );
		} );
		it( "it should load a map if the Map div is present", function() {
			// Start loading page
			page.start();
			// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
			waitsFor( function() {
				// when this function returns true the function in the runs() call is executed
				return $( '#ai1ec-gmap-canvas' ).html() !== '';
			}, "gmaps never loaded", 15000 );
			runs( function() {
				expect( $('#ai1ec-gmap-canvas') ).not.toBeEmpty();
			} );
		} );
		it( "Should hide the map and make the placeholder visible until the user clicks on the placeholder", function() {
			// Start loading page
			page.start();
			// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
			waitsFor( function() {
				// when this function returns true the function in the runs() call is executed
				return $( '#ai1ec-gmap-canvas' ).html() !== '';
			}, "gmaps never loaded", 15000 );
			runs( function() {
				expect( $('#ai1ec-gmap-canvas') ).toHaveCss( { visibility : "hidden" } );
				$( '.ai1ec-gmap-placeholder:first' ).trigger( 'click' );
				expect( $('#ai1ec-gmap-canvas') ).toHaveCss( { visibility : "visible" } );
				expect( $( '.ai1ec-gmap-placeholder' ) ).not.toExist();
			} );
		} );
	} );
} );

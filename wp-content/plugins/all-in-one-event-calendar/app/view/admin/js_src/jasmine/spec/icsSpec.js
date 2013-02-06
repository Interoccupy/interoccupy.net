require( [
         "jquery",
         'domReady',
         'scripts/calendar_feeds/ics/ics_event_handlers',
         'scripts/calendar_feeds/ics/ics_utility_functions',
         'scripts/calendar_feeds/ics/ics_ajax_handlers',
         "libs/utils",
         'scripts/calendar_feeds',
         'external_libs/bootstrap_tab',
         'external_libs/bootstrap_modal'
         ],
         function ( $, domReady, event_handlers, utility_functions, ajax_handlers, AI1EC_UTILS, page ) {
	"use strict"; // jshint ;_;
	// Globalize jQuery
	window.jQuery = $;
	window.$ = $;
	// Set the ajaxurl variable for testing purpose
	var ajaxurl = "http://localhost/wordpress/wp-admin/admin-ajax.php";
	describe( "ICS Feeds page", function() {
		// This is the setup method
		beforeEach( function() {
			// Load the HTML
			loadFixtures( 'calendar_feeds/ics_feed.html' );
			// Set up the listeners
			page.start();
		} );
		describe( "Test suite for the 'Add new feed button'", function() {
			it( "Should add an error div and change the border color when you add the same url twice", function() {
				// Set the input field
				$( '#ai1ec_feed_url' ).val( 'https://www.google.com/calendar/ical/nicola.peluchetti%40gmail.com/public/basic.ics' );
				// Trigger the button
				$( '#ai1ec_add_new_ics' ).click();
				// You should have an error div
				expect( $( '#ai1ec-feed-error' ) ).toExist();
				// Check it's text
				expect( $( '#ai1ec-feed-error' ) ).toHaveText( ai1ec_requirejs.duplicate_feed_message );
				// Check the border. There are inconsistencies in jQuery so i use border-left-color which works cross browser
				// see https://groups.google.com/group/jquery-en/browse_thread/thread/bc05e70abe3e915e%3C/a%3E
				expect( $( '#ai1ec_feed_url' ) ).toHaveCss( { 'border-left-color' : '#FF0000' } );
				expect( $( '.ai1ec-feed-container .ai1ec-feed-url:text' ) ).toHaveCss( { 'border-left-color' : '#FF0000' } );
			} );
			it( "Should add an error DIV and change the border color if an invalid url is supplied", function() {
				// Set the input field
				$( '#ai1ec_feed_url' ).val( 'dsffskl' );
				// Trigger the button
				$( '#ai1ec_add_new_ics' ).click();
				// You should have an error div
				expect( $( '#ai1ec-feed-error' ) ).toExist();
				// Check it's text
				expect( $( '#ai1ec-feed-error' ) ).toHaveText( ai1ec_requirejs.invalid_url_message );
				expect( $( '#ai1ec_feed_url' ) ).toHaveCss( { 'border-left-color' : '#FF0000' } );
			} );
			it( "Should disable the button and make an AJAX call when a valid url is inserted", function() {
				// Set the input field
				$( '#ai1ec_feed_url' ).val( 'https://www.google.com/calendar/ical/nicola.peluchetti%40gmail.com/public/bic.ics' );
				// Spy on the post event
				var post_spy = spyOn( $, 'post' );
				// Trigger the button
				$( '#ai1ec_add_new_ics' ).click();
				expect( $( '#ai1ec_add_new_ics' ) ).toHaveProp( 'disabled' );
				// create the data to send
				var data = {
					action: 'ai1ec_add_ics',
					feed_url: 'https://www.google.com/calendar/ical/nicola.peluchetti%40gmail.com/public/bic.ics',
					feed_category: '8',
					feed_tags: ''
				};
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data, ajax_handlers.handle_add_new_ics, 'json' );
			} );
			it( "Should add a new feed, hide the spinner and re-enable the button after a successfull AJAX call", function() {
				// Set the input field
				$( '#ai1ec_feed_url' ).val( 'https://www.google.com/calendar/ical/nicola.peluchetti%40gmail.com/public/bic.ics' );
				// Create fake data
				var fake_data = {
						error : false,
						message : "<div id='fake'></div>"
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_data );
				});
				// Trigger the button
				$( '#ai1ec_add_new_ics' ).click();
				// get the element that should be the new feed.
				var $new_feed = $( '#ai1ec-feeds-after' ).next();
				expect( $( '#ai1ec_add_new_ics' ) ).toHaveProp( 'disabled', false );
				expect( $( '#ai1ec_feed_url' ) ).toHaveValue( '' );
				expect( $new_feed ).toBe( 'div#fake' );
			} );
			it( "Should alert the error message if an error is returned", function() {
				// Set the input field
				$( '#ai1ec_feed_url' ).val( 'https://www.google.com/calendar/ical/nicola.peluchetti%40gmail.com/public/bic.ics' );
				// Create fake data
				var fake_data = {
						error : true,
						message : "nasty errors"
				};
				var alert_spy = spyOn( window, 'alert' );
				// call andCallFake so that it calls the success function.
				// Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_data );
				});
				// Trigger the button
				$( '#ai1ec_add_new_ics' ).click();
				expect( alert_spy ).toHaveBeenCalledWith( fake_data.message );
			} );
		} );
		describe( "Test Suite for the remove feed button", function() {
			it( "Should open a modal window when you click on the delete feed button", function() {
				var modal_spy = spyOn( $.fn, 'modal' );
				// Start the test
				$( '.ai1ec_delete_ics' ).click();
				var data_for_modal = {
						"show": true,
						"backdrop" : true
				};
				expect( modal_spy ).toHaveBeenCalledWith( data_for_modal );
				expect( $( '#ai1ec-ics-modal a.btn' ) ).toHaveData( 'el', $( '.ai1ec_delete_ics' ).get(0) );
			} );
			it( "Should make an ajax call when you click on a button of the modal", function() {
				// create the data to send
				var data = {
					"action" : 'ai1ec_delete_ics',
					"ics_id" : '31',
					"remove_events" : true
				};
				// Spy on the post event
				var post_spy = spyOn( $, 'post' );
				// Start the test
				$( '.ai1ec_delete_ics' ).click();
				// CLick on the remove events button in the modal
				$( '#ai1ec-ics-modal a.btn-danger' ).click();
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data, ajax_handlers.handle_delete_ics, 'json' );
			} );
			it( "Should restore normal state if an error is returned and display a message", function() {
				// Create fake data
				var fake_data = {
						error   : true,
						message : "nasty errors",
						ics_id  : 31
				};
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				var restore_state_spy = spyOn( utility_functions, 'restore_normal_state_after_unsuccesful_delete' ).andCallThrough();
				// call andCallFake so that it calls the success function.
				// Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_data );
				});
				var $hidden = $( 'input[value=' + fake_data.ics_id + ']' );
				// Start the test
				$( '.ai1ec_delete_ics' ).click();
				// Click on the remove button
				$( '#ai1ec-ics-modal .remove' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_data.message, 'error' );
				expect( restore_state_spy ).toHaveBeenCalledWith( $hidden );
				expect( $( '.ai1ec_delete_ics ' ) ).toHaveProp( 'disabled', false );
			} );
			it( "Should delete the feed if ok is returned and display a message", function() {
				// Create fake data
				var fake_data = {
						error   : false,
						message : "fucking cool",
						ics_id  : 31
				};
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				var remove_feed_from_dom_spy = spyOn( utility_functions, 'remove_feed_from_dom' ).andCallThrough();
				// call andCallFake so that it calls the success function.
				// Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_data );
				});
				var $hidden = $( 'input[value=' + fake_data.ics_id + ']' );
				// Start the test
				$( '.ai1ec_delete_ics' ).click();
				// Click on the remove button
				$( '#ai1ec-ics-modal .remove' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_data.message, 'success' );
				expect( remove_feed_from_dom_spy ).toHaveBeenCalledWith( $hidden );
				expect( $( '.ai1ec-feed-container' ).not( '#ai1ec-feeds-after' ) ).not.toExist();
			} );
		} );
		describe( "Test suite for the update feed button", function() {
			it(  "It should make a call to $.post and disable the button", function() {
				// create the data to send
				var data = {
						action: 'ai1ec_update_ics',
						ics_id: '31'
				};
				// Spy on the post event
				var post_spy = spyOn( $, 'post' );
				// Start the test
				$( '.ai1ec_update_ics' ).click();
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data, ajax_handlers.handle_update_ics, 'json' );
				expect( $( '.ai1ec_update_ics' ) ).toHaveProp( 'disabled', true );
			} );
			it( "should create an alert on success", function() {
				// Create fake data
				var fake_data = {
						error   : false,
						message : "fucking cool",
						ics_id  : 31
				};
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				// call andCallFake so that it calls the success function.
				// Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_data );
				});
				// Start the test
				$( '.ai1ec_update_ics' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_data.message, 'success' );
				expect( $( '.ai1ec_update_ics ' ) ).toHaveProp( 'disabled', false );
			} );
			it( "should create an alert on error", function() {
				// Create fake data
				var fake_data = {
						error   : true,
						message : "nasty things",
						ics_id  : 31
				};
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				// call andCallFake so that it calls the success function.
				// Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_data );
				});
				// Start the test
				$( '.ai1ec_update_ics' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_data.message, 'error' );
				expect( $( '.ai1ec_update_ics ' ) ).toHaveProp( 'disabled', false );
			} );
		} );
	} );
} );

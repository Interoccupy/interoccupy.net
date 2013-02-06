require( [
         "jquery",
         'domReady',
         'scripts/calendar_feeds/facebook/facebook_event_handlers',
         'scripts/calendar_feeds/facebook/facebook_utility_functions',
         'scripts/calendar_feeds/facebook/facebook_ajax_handlers',
         "libs/utils",
         'scripts/calendar_feeds',
         'external_libs/bootstrap_tab',
         "external_libs/jquery_cookie"],
         function ( $, domReady, event_handlers, utility_functions, ajax_handlers, AI1EC_UTILS, page ) {
	"use strict"; // jshint ;_;
	// Globalize jQuery
	window.jQuery = $;
	window.$ = $;
	// Set the ajaxurl variable for testing purpose
	var ajaxurl = "http://localhost/wordpress/wp-admin/admin-ajax.php";
	describe( "Facebook Feeds page", function() {
		// This is the setup method
		beforeEach( function() {
			// Load the HTML
			loadFixtures( 'calendar_feeds/facebook_feed.html' );
			// Set up the listeners
			page.start();
		} );
		describe( "Test suite for the tabs behaviour: activation on first load, and calling jQuery cookie", function() {
			it( "Should call jQuery cookie to save the href ", function() {
				// Set up spies
				var cookieSpy = spyOn( $, 'cookie' );
				// Start the test
				$( 'a[href=#facebook]' ).click();
				// verify that it has been called with the correct parameter
				expect( cookieSpy ).toHaveBeenCalledWith( 'feeds_active_tab', '#facebook' );
			} );
			it( "Should show the first page if no cookie is present and null is passed to the function", function() {
				// Spy on the tab() method
				var tab = spyOn( $.fn, 'tab' );
				// Execute the function
				utility_functions.activate_saved_tab_on_page_load( null );
				// Check that the tab() method was called with the right parameter
				expect( tab ).toHaveBeenCalledWith( 'show' );
				// Check the jQuery selector was what we expected
				expect( tab.mostRecentCall.object.selector ).toEqual( 'ul.nav-tabs a:first' );
			} );
			it( "Should show the tab with the same href as that passed to the function", function() {
				// Spy on the tab() method
				var tabSpy = spyOn( $.fn, 'tab' );
				// Execute the function
				utility_functions.activate_saved_tab_on_page_load( '#ics' );
				// Check that the tab() method was called with the right parameter
				expect( tabSpy ).toHaveBeenCalledWith( 'show' );
				// Check the jQuery selector was what we expected
				expect( tabSpy.mostRecentCall.object.selector ).toEqual( 'ul.nav-tabs a[href=#ics]' );
			} );
		} );
		describe( "Test suite for the correct functionioning of the 'Subscribe Users' button", function() {
			it( "Should call an alert if no option was selected", function() {
				// Set the spy
				var alertSpy = spyOn( window, 'alert' );
				// Start
				var but = $( '#ai1ec_subscribe_users' );
				but.trigger( 'click' );
				// check that the alert was called
				expect( alertSpy ).toHaveBeenCalledWith( 'Select at least one user group \/ page to subscribe' );
			} );
			it( "Should call an alert if more than 10 option were selected", function() {
				// Set the spy
				var alertSpy = spyOn( window, 'alert' );
				// Select the first option
				var opt = $( '#ai1ec-facebook-user option:first' );
				// Select the 15th option
				var opt15 = $( '#ai1ec-facebook-user option:eq(13)' );
				// Select all the options
				opt.nextUntil( opt15 ).prop( 'selected', true );
				// Trigger the button
				var but = $( '#ai1ec_subscribe_users' );
				but.trigger( 'click' );
				// check that the alert was called
				expect( alertSpy ).toHaveBeenCalledWith( 'Please select no more than ten users \/ groups \/ pages at a time to avoid overloading Facebook Requests' );
			} );
			it( "Should call the function that blocks all imputs in other cases", function() {
				// Set the spy
				var blockSpy = spyOn( utility_functions, 'block_all_submit_and_ajax' );
				// Select the first option
				$( '#ai1ec-facebook-user option:first' ).prop( 'selected', true );
				// Trigger the button
				var but = $( '#ai1ec_subscribe_users' );
				but.trigger( 'click' );
				// check that the function was called and that the button element was passed
				expect( blockSpy ).toHaveBeenCalledWith( but.get(0) );
			} );
		} );
		describe( "Test suite for the block_all_submit_and_ajax function. When invoked:", function() {
			it( "Clones the passed submit to a hidden field", function() {
				// Get the button
				var $button = $( '#ai1ec_subscribe_users' );
				// Call the function
				utility_functions.block_all_submit_and_ajax( $button.get(0) );
				// Get the next element
				var $hidden = $button.next().get(0);
				// Check it's an hidden input of the same name of the passed button
				expect( $hidden ).toBe( 'input' );
				expect( $hidden ).toBeHidden();
				expect( $hidden ).toHaveAttr( 'name', $button.attr( 'name' ) );
			} );
			it( "Disable all submit buttons", function() {
				// Get a button
				var $button = $( '#ai1ec_subscribe_users' );
				// Call the function
				utility_functions.block_all_submit_and_ajax( $button.get(0) );
				waits(20);
				// check that all submit are disabled
				runs( function() {
					$( '#facebook input[type=submit]' ).each( function( i, el ) {
						expect( el ).toHaveAttr( 'disabled' );
					} );
				} );
			} );
			it( "Prevent all ajax functionality", function() {
					// Get a button
					var $button = $( '#ai1ec_subscribe_users' );
					// Call the function
					utility_functions.block_all_submit_and_ajax( $button.get(0) );
					// check that all submit are disabled
					spyOnEvent( $('.ai1ec-facebook-refresh-multiselect:first'), 'click' );
					$( '.ai1ec-facebook-refresh-multiselect:first' ).click();
					expect( 'click' ).toHaveBeenTriggeredOn( $('.ai1ec-facebook-refresh-multiselect:first') );
					expect( 'click' ).toHaveBeenPreventedOn( $('.ai1ec-facebook-refresh-multiselect:first') );
			} );
		});
		describe( "Test suite for checking functionality of submit buttons", function() {
			it( "Should call block_all_submit_and_ajax when clicking a submit button", function() {
				// Set the spy
				var blockSpy = spyOn( utility_functions, 'block_all_submit_and_ajax' );
				// Iterate on the submits
				$( 'input[type=submit]' ).not( '#ai1ec_subscribe_users' ).each( function( i, el ) {
					$( el ).click();
					expect( blockSpy ).toHaveBeenCalledWith( el );
				} );
			} );
		} );
		describe( "Test suite for the refresh multiselect action", function() {
			it( "Should call the $.post method to make a call", function() {
				var postSpy = spyOn( $, 'post' );
				// Click the user multiselect
				$( '.ai1ec-facebook-refresh-multiselect:first' ).click();
				// Create the data to be posted
				var data = {
						"action"     : 'ai1ec_refresh_facebook_objects',
						"ai1ec_type" : $( '.ai1ec-facebook-refresh-multiselect:first' ).closest( '.ai1ec-facebook-multiselect-container' ).data( 'type' )
				};
				// Check if the function was called.
				expect( postSpy ).toHaveBeenCalledWith( ajaxurl, data, ajax_handlers.handle_refresh_multiselect, 'json' );
			} );
			it( "Should create an error message if an error is returned", function() {
				// Spy on the make_alert function
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors" : true,
						"error_messages" : ["message1", "message2"],
						"type" : "user"
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Start the test
				$( '.ai1ec-facebook-refresh-multiselect:first' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( 'message1<br/>message2', 'error' );
			} );
			it( "Should create a success message if everything goes well and replace the multiselect", function() {
				// Spy on the make_alert function
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				// Spy on replaceWith
				var replace_spy = spyOn( $.fn, "replaceWith" );
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors" : false,
						"message" : "everything cool",
						"type" : "user",
						"html" : "fake html"
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Start the test
				$( '.ai1ec-facebook-refresh-multiselect:first' ).click();
				// Check that was created the correct alert
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_return.message, 'success' );
				// Check that the replace was correct
				expect( replace_spy ).toHaveBeenCalledWith( fake_return.html );
				// check that the correct selector was used
				expect( replace_spy.mostRecentCall.object.selector ).toEqual( ' .ai1ec-facebook-multiselect-container[data-type=' + fake_return.type + '] .ai1ec-facebook-multiselect' );
			} );
		} );
		describe( "Test suite for the 'refresh events from facebook' button", function() {
			var make_alert_spy;
			beforeEach( function() {
				// Spy on the make_alert function
				make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
			} );
			it( "Should make an ajax call to the server to fetch the data for the events", function() {
				var postSpy = spyOn( $, 'post' );
				// Click the refresh button
				$( '.ai1ec-facebook-refresh[data-id=1545996751]' ).click();
				var data = {
						"action"        : 'ai1ec_refresh_events',
						"ai1ec_post_id" : 1545996751,
						"ai1ec_type"    : 'user'
					};
				expect( postSpy ).toHaveBeenCalledWith( ajaxurl, data, ajax_handlers.handle_refresh_events, 'json' );
			} );
			it( "Should create an error alert if an exception occured", function() {
				// Create fake data to pass to the success handler
				var fake_return = {
						"exception" : true,
						"message" : "nothing cool",
						"id" : 1545996751
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the refresh button
				$( '.ai1ec-facebook-refresh[data-id=1545996751]' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_return.message, 'error' );
			} );
			it( "Should create a warning message if no exception but only errors occured", function() {
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors" : true,
						"message" : "nothing cool",
						"id" : 1545996751
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the refresh button
				$( '.ai1ec-facebook-refresh[data-id=1545996751]' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_return.message, 'warning' );
			} );
			it( "Should create a success message if everything went well", function() {
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors" : false,
						"message" : "everything cool",
						"id" : 1545996751
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the refresh button
				$( '.ai1ec-facebook-refresh[data-id=1545996751]' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_return.message, 'success' );
			} );
			it( "Should create an alert and append it to the DOM", function() {
				var append_spy = spyOn( $.fn, 'append' );
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors" : false,
						"message" : "everything cool",
						"id" : 1545996751
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the refresh button
				$( '.ai1ec-facebook-refresh[data-id=1545996751]' ).click();
				var $alert = AI1EC_UTILS.make_alert( fake_return.message, 'success' );
				expect( append_spy ).toHaveBeenCalledWith( $alert );
				// Check the jQuery selector was what we expected
				expect( append_spy.mostRecentCall.object.selector ).toEqual( '#alerts' );
			} );
		} );
		describe( "Test suite for the 'remove subscription from Facebook' button", function() {
			it( "Should open a modal dialog that asks if you want to keep or remove the user events and holds some data.", function() {
				var modal_spy = spyOn( $.fn, 'modal' );
				// Click the remove button
				$( '.ai1ec-facebook-remove[data-id=1545996751]' ).click();
				var options = {
						"show": true,
						"backdrop" : true
				};
				// Check that the modal has been called
				expect( modal_spy ).toHaveBeenCalledWith( options );
				// Check that the correct values are set
				expect( $( '#ai1ec-facebook-modal a.btn' ) ).toHaveData( 'user_id', 1545996751 );
				expect( $( '#ai1ec-facebook-modal a.btn' ) ).toHaveData( 'el', $( '.ai1ec-facebook-remove[data-id=1545996751]' )[0] );
				expect( $( '#ai1ec-facebook-modal a.btn' ) ).toHaveData( 'logged', false );
				// Check that the correct text was set
				expect( $( '#ai1ec-facebook-modal #ai1ec-facebook-user-modal' ) ).toHaveText( "Achille Balestrini" );
			} );
			it( "Should send an ajax request to the server after clicking on a button on the modal", function() {
				var postSpy = spyOn( $, 'post' );
				// Click the remove button
				$( '.ai1ec-facebook-remove[data-id=1545996751]' ).click();
				// Click on the remove events button in the modal
				$( '#ai1ec-facebook-modal a.btn-danger' ).click();
				var data = {
						"action"              : 'ai1ec_remove_subscribed',
						"ai1ec_post_id"       : 1545996751,
						"ai1ec_remove_events" : true,
						"ai1ec_logged_user"   : false,
						"type"                : 'user'
					};
				expect( postSpy ).toHaveBeenCalledWith( ajaxurl, data, ajax_handlers.handle_remove_events, 'json' );
			} );
			it( "Should show an alert if errors are returned from the server", function() {
				// Spy on the make_alert function
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors"        : true,
						"error_message" : "nothing cool",
						"id"            : 1545996751,
						"logged"        : "false",
						"type"          : 'user'
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the remove button
				$( '.ai1ec-facebook-remove[data-id=1545996751]' ).click();
				// CLick on the remove events button in the modal
				$( '#ai1ec-facebook-modal a.btn-danger' ).click();
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_return.error_message, 'error' );
			} );
			it( "should call the cancel_element_and_reorder_other, create an alert and update the multiselect if the user that was unsubscribed is not the currently logged user", function() {
				var cancel_element_spy = spyOn( utility_functions, 'cancel_element_and_reorder_other' );
				// Spy on the make_alert function
				var make_alert_spy = spyOn( AI1EC_UTILS, 'make_alert' );
				// Spy on the replaceWith() method
				var replace_with_spy = spyOn( $.fn, "replaceWith" ).andCallThrough();
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors"  : false,
						"message" : "everything cool",
						"id"      : 1545996751,
						"logged"  : "false",
						"type"    : "user",
						"html"    : "<select></select>"
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the remove button
				$( '.ai1ec-facebook-remove[data-id=1545996751]' ).click();
				// CLick on the remove events button in the modal
				$( '#ai1ec-facebook-modal a.btn-danger' ).click();
				// Check that all the functions were called with the correct parameters
				expect( cancel_element_spy ).toHaveBeenCalledWith( $( '.ai1ec-facebook-remove[data-id=1545996751]' ) );
				expect( make_alert_spy ).toHaveBeenCalledWith( fake_return.message, 'success' );
				expect( replace_with_spy ).toHaveBeenCalledWith( fake_return.html );
				expect( $('#ai1ec-facebook-user') ).not.toExist();

			} );
			it( "should create an alert, remove the category and refresh / remove buttons and show the 'subscribe to your events' if the user that was unsubscribed is  the currently logged user", function() {
				// Create fake data to pass to the success handler
				var fake_return = {
						"errors"  : false,
						"message" : "everything cool",
						"id"      : 614241161,
						"logged"  : "true",
						"type"    : 'user'
				};
				// call andCallFake so that it calls the success function. Note that andCallFake receive the same parameters as the spied function
				spyOn( $, "post" ).andCallFake( function( url, data, success ) {
					success( fake_return );
				});
				// Click the remove button of the logged user
				$( '.ai1ec-facebook-remove[data-id=614241161]' ).click();
				// CLick on the remove events button in the modal
				$( '#ai1ec-facebook-modal a.btn-danger' ).click();
				// The remove button should not be there
				expect( $( '.ai1ec-facebook-remove[data-id=614241161]' ) ).not.toExist();
				// Neither the refresh
				expect( $( '.ai1ec-facebook-refresh[data-id=614241161]' ) ).not.toExist();
				// Nor the tag - category wrapper
				expect( $( '#ai1ec-facebook .ai1ec-facebook-category-tag-wrapper' ) ).not.toExist();
				// while the button should be shown
				expect( $( '#ai1ec-ai1ec_facebook_subscribe_yours ' ) ).not.toBeHidden();
			} );
		} );
		describe( "Test suite for the cancel_element_and_reorder_other function", function() {
			it( "Should delete the closest container", function() {
				// trigger the function with a user
				utility_functions.cancel_element_and_reorder_other( $( '.ai1ec-facebook-remove[data-id=1545996751]' ) );
				// Check that the element was removed
				expect( $( '.ai1ec-facebook-remove[data-id=1545996751]' ).closest( '.span4' ) ).not.toExist();
			} );
			it( "Should show a message if the last container was deleted", function() {
				// trigger the function with the last user and check that the empty message is displayed
				utility_functions.cancel_element_and_reorder_other( $( '.ai1ec-facebook-remove[data-id=145425822198094]' ) );
				expect( $( '.ai1ec-facebook-items[data-type=page] .no_subscription' ) ).toHaveText( ai1ec_requirejs.no_more_subscription );
			} );
		} );
	} );

} );

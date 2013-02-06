require( [
         "jquery",
         'domReady',
         'scripts/add_new_event/event_location/gmaps_helper',
         "libs/utils",
         'scripts/add_new_event',
         'external_libs/jquery.calendrical_timespan',
         'external_libs/jquery.inputdate',
         'external_libs/jquery.tools',
         'external_libs/jquery.blockui',
         'external_libs/ai1ec_datepicker'
         ],
         function ( $, domReady, gmaps_helper, AI1EC_UTILS, page, calendrical_functions ) {
	"use strict"; // jshint ;_;
	// Globalize jQuery
	window.jQuery = $;
	window.$ = $;
	// Set the ajaxurl variable for testing purpose
	var ajaxurl = "http://localhost/wordpress/wp-admin/admin-ajax.php";
	describe( "Add new event page", function() {
		afterEach(  function() {
			// Unset any handlers that are delegated to the window object. This is important otherwise every time page.start() is called all the handlers are bound one more time
			$( window ).off();
		});
		describe( "Test suite for the Additional publish button", function() {
			// This is the setup method
			beforeEach( function() {
				// Load the HTML
				loadFixtures( 'add_new_event/add_new_event.html' );
				// Set up the listeners
				page.start();
				// Prevent the form from submitting just for the test
				$( 'form#post' ).on( 'submit.jasminetest', function() {
					return false;
				});

			} );
			afterEach( function() {
				$( 'form#post' ).off( 'submit.jasminetest' );
			} );
			it( "Should trigger the click action on the publish button", function() {
				var spyEvent = spyOnEvent( '#publish', 'click' );
				$( '#ai1ec_bottom_publish' ).click();
				expect( spyEvent ).toHaveBeenTriggered();
			} );
		} );
		describe( "Test suite for the 'Event location' Box", function() {
			// This is the setup method
			beforeEach( function() {
				// Load the HTML
				loadFixtures( 'add_new_event/add_new_event.html' );
				// Set up the listeners
				page.start();
			} );
			it( "Should show the map when the appropriate checkbok is clicked and should hide it when clicked again", function() {
				// click on the checkbox
				$( "#ai1ec_google_map" ).click();
				// Expect the map to be visible
				expect( $( '#ai1ec_map_canvas' ) ).toBeVisible();
				// Uncheck the checkbox
				$( "#ai1ec_google_map" ).click();
				// Expect the map to be hidden
				expect( $( '#ai1ec_map_canvas' ) ).not.toBeVisible();
			} );
			it( "Should show the input coordinates table when the appropriate checkbok is clicked and should hide it when clicked again", function() {
				// click on the checkbox
				$( "#ai1ec_input_coordinates" ).click();
				// Expect the table to be visible
				expect( $( '#ai1ec_table_coordinates' ) ).toBeVisible();
				// uncheck the checkbox
				$( "#ai1ec_input_coordinates" ).click();
				// expect the table to be hidden
				expect( $( '#ai1ec_table_coordinates' ) ).toHaveCss( { visibility : 'hidden' } );
			}
			);
			it( "Should clear the value of the longitude / latitude fields when the checkbox is unchecked", function() {
				// show the coordinares
				$( "#ai1ec_input_coordinates" ).click();
				// set a value for latitude and longitude
				$( "#ai1ec_latitude" ).val( 93 );
				$( "#ai1ec_longitude" ).val( 193 );
				// uncheck the coordinates
				$( "#ai1ec_input_coordinates" ).click();
				// Expect the field value to have been canceled
				expect( $( "#ai1ec_latitude" ) ).toHaveValue( '' );
				expect( $( "#ai1ec_longitude" ) ).toHaveValue( '' );
			} );
			it( "Should show an error after the latitude field if the latitude is not valid and blur is triggered", function() {
				// show the coordinates
				$( "#ai1ec_input_coordinates" ).click();
				// Set an invalid latitude
				$( "#ai1ec_latitude" ).val( 93 );
				// Trigger the blur event
				$( "#ai1ec_latitude" ).trigger( 'blur' );
				// There should be an error
				expect( $( "#ai1ec_latitude" ).next() ).toBe( 'div.ai1ec-error' );
				expect( $( "#ai1ec_latitude" ).next() ).toHaveText( ai1ec_requirejs.error_message_not_valid_lat );
			} );
			it( "Should show an error after the longitude field if the longitude is not valid and blur is triggered", function() {
				// show the coordinates
				$( "#ai1ec_input_coordinates" ).click();
				// Set an invalid longitude
				$( "#ai1ec_longitude" ).val( 193 );
				// Trigger the blur event
				$( "#ai1ec_longitude" ).trigger( 'blur' );
				// There should be an error
				expect( $( "#ai1ec_longitude" ).next() ).toBe( 'div.ai1ec-error' );
				expect( $( "#ai1ec_longitude" ).next() ).toHaveText( ai1ec_requirejs.error_message_not_valid_long );
			} );
			it( "Should show no error when latitude / longitude are ok and blur is triggered", function() {
				$( "#ai1ec_input_coordinates" ).click();
				$( "#ai1ec_longitude" ).val( 163 );
				$( "#ai1ec_longitude" ).trigger( 'blur' );
				expect( $( "#ai1ec_longitude" ).next() ).not.toBe( 'div.ai1ec-error' );
				$( "#ai1ec_input_coordinates" ).click();
				$( "#ai1ec_latitude" ).val( 63 );
				$( "#ai1ec_latitude" ).trigger( 'blur' );
				expect( $( "#ai1ec_latitude" ).next() ).not.toBe( 'div.ai1ec-error' );
			} );
			it( "Should  trigger the function that updates the map only when blur is triggered and both long / lat fields are set with valid data", function() {
				// Set up spy
				var update_map_spy = spyOn( gmaps_helper, 'ai1ec_update_map_from_coordinates' );
				$( "#ai1ec_input_coordinates" ).click();
				// not valid longitude
				$( "#ai1ec_longitude" ).val( 183 );
				$( "#ai1ec_latitude" ).val( 63 );
				$( "#ai1ec_latitude" ).trigger( 'blur' );
				// It should not call the function
				expect( update_map_spy ).not.toHaveBeenCalled();
				// Valid longitude
				$( "#ai1ec_longitude" ).val( 153 );
				$( "#ai1ec_longitude" ).trigger( 'blur' );
				// It should call the function
				expect( update_map_spy ).toHaveBeenCalled();
			} );
			it( "The map marker should be updated the with lat / long data if lat / long are valid ", function() {
				// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
				waitsFor( function() {
					// when this function returns true the function in the runs() call is executed
					return $('#ai1ec_map_canvas').html() !== '';
				}, "gmaps never loaded", 15000 );
				runs( function() {
					// Show the coordinated
					$( "#ai1ec_input_coordinates" ).click();
					// enter valid lat / long
					$( "#ai1ec_longitude" ).val( 143 );
					$( "#ai1ec_latitude" ).val( 63 );
					$( "#ai1ec_latitude" ).trigger( 'blur' );
					// The marker has the values we set
					expect( gmaps_helper.get_marker().position.lat() ).toEqual( 63 );
					expect( gmaps_helper.get_marker().position.lng() ).toEqual( 143 );
				} );
			} );
			if( !! navigator.geolocation ) {
				// This test fails on Safari if the Wi-Fi is turned off as safari needs Wi-Fi to get the position of things.
				it( "The map should be initialized with data from the geolocator if it is present and no coordinates are set", function() {
					// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
					waitsFor( function() {
						return typeof gmaps_helper.get_position() !== 'undefined';
					}, "gmaps never loaded", 15000 );
					waits( 100 );
					runs( function() {
						// I use toBeCloseTo() as there can be some roundings done by google
						expect( gmaps_helper.get_marker().position.lat() ).toBeCloseTo( gmaps_helper.get_position().coords.latitude, 4 );
						expect( gmaps_helper.get_marker().position.lng() ).toBeCloseTo( gmaps_helper.get_position().coords.longitude, 4 );
					} );
				} );
			} else {
				it( "The map should be initialized with standard data if the geolocator is not present and no coordinates are set", function() {
					// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
					waitsFor( function() {
						return $('#ai1ec_map_canvas').html() !== '';
					}, "gmaps never loaded", 15000 );
					runs( function() {
						// These are the standard coordinates we use
						expect( gmaps_helper.get_marker().position.lat() ).toEqual( 9.965 );
						expect( gmaps_helper.get_marker().position.lng() ).toEqual( -83.327 );
					} );
				} );
			}
			it( "Should show some suggestions when entering a value in the address field and format the result after clicking on it. It also fills some hidden fields.", function() {
				$( '#ai1ec_address' ).val( 'Via Andrea del Sarto' );
				// We need to trigger keypress in opera!
				$( '#ai1ec_address' ).trigger( 'keydown' ).trigger( 'keypress' );
				waitsFor( function() {
					// Wait for the result to appear
					return $( 'div.ac_results li' ).length > 0;
				}, "geo_autocomplete never triggered", 10000 );
				// Clean up the geoautocompleter results after the test
				this.after( function() {
					$( 'div.ac_results' ).remove();
				} );
				runs( function() {
					// ten results are returned
					expect( $( 'div.ac_results li' ).length ).toEqual( 10 );
					// click on the first
					$( 'div.ac_results li:first' ).click();
					// The address fields get this value
					expect( $( '#ai1ec_address' ) ).toHaveValue( 'Via Andrea del Sarto, 58100 Province of Grosseto, Italy' );
					// Check the hidden fields
					expect( $( '#ai1ec_country' ) ).toHaveValue( 'Italy' );
					expect( $( '#ai1ec_postal_code' ) ).toHaveValue( '58100' );
					expect( $( '#ai1ec_province' ) ).toHaveValue( 'Tuscany' );
					expect( $( '#ai1ec_city' ) ).toHaveValue( '' );
				} );
			} );
			it( "Should prevent the geo_autocomplete from beeing triggered when the input coordinates checkbox is checked", function() {
				// Check the checkbox
				$( "#ai1ec_input_coordinates" ).click();
				// Try to trigger the autocomplete
				$( '#ai1ec_address' ).val( 'Via Andrea del Sarto' );
				// We need to trigger keypress in opera!
				$( '#ai1ec_address' ).trigger( 'keydown' ).trigger( 'keypress' );
				// wait a little
				waits( 3000 );
				runs( function() {
					expect( $( 'div.ac_results' ) ).not.toExist();
				} );
			} );
			it( "Should show a warning when latitude or longitude are not both set and the publish button is pressed", function() {
				// Check the checkbox
				$( "#ai1ec_input_coordinates" ).click();
				// Set the latitude
				$( "#ai1ec_latitude" ).val( 93 );
				// Click publish
				$( '#publish' ).click();
				expect( $( "#ai1ec_longitude" ).next() ).toBe( 'div.ai1ec-error' );
				expect( $( "#ai1ec_longitude" ).next() ).toHaveText( ai1ec_requirejs.error_message_not_entered_long );
			} );
		} );
		describe( "Test suite for the 'Event location' box when some data is already present", function() {
			it( "Should set the coordinates of the marker using the data from the input fields if present", function() {
				// Load the HTML
				loadFixtures( 'add_new_event/event_location/event_location_with_coordinates.html' );
				// Set up the listeners
				page.start();
				// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
				waitsFor( function() {
					return $('#ai1ec_map_canvas').html() !== '';
				}, "gmaps never loaded", 15000 );
				runs( function() {
					// in the html the value 3 and 6 are hardcoded
					expect( gmaps_helper.get_marker().position.lat() ).toEqual( 3 );
					expect( gmaps_helper.get_marker().position.lng() ).toEqual( 6 );
				} );
			} );
			it( "Should set the coordinates of the marker using the data from the address fields if present", function() {
				// Load the HTML
				loadFixtures( 'add_new_event/event_location/event_location_with_address.html' );
				// Set up the listeners
				page.start();
				// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
				waitsFor( function() {
					return $('#ai1ec_map_canvas').html() !== '';
				}, "gmaps never loaded", 15000 );
				waits( 2000 );
				runs( function() {
					expect( gmaps_helper.get_marker().position.lat() ).toBeCloseTo( 45.471, 3 );
					expect( gmaps_helper.get_marker().position.lng() ).toBeCloseTo( 9.221, 3 );
				} );
			} );
			it( "Should set the coordinates of the marker using the data from the coordinates input fields if both fields and address are present", function() {
				// Load the HTML
				loadFixtures( 'add_new_event/event_location/event_location_with_coordinates_and_address.html' );
				// Set up the listeners
				page.start();
				// Wait for the loading of the gmaps. This is simply when the <div> we use as a container has some html
				waitsFor( function() {
					return $('#ai1ec_map_canvas').html() !== '';
				}, "gmaps never loaded", 15000 );
				runs( function() {
					expect( gmaps_helper.get_marker().position.lat() ).toEqual( 3 );
					expect( gmaps_helper.get_marker().position.lng() ).toEqual( 6 );
				} );
			} );

		} );
		describe( "Test suite for the 'event date and time' box ", function() {
			// This is the setup method
			beforeEach( function() {
				// Load the HTML
				loadFixtures( 'add_new_event/add_new_event.html' );
				// Set up the listeners
				page.start();
			} );
			it( "should initialize the start end end date based on the current time ( as passed by wordpress ) and formatted - This test might need adjustment because of your timezone", function() {
				// The date in the specrunner file is Tue Jun 19 2012 17:36:37 GMT+0200 so i'd expect 15:30 - 16:30
				// We do not test further the plugin, this is just to test that it initialized correctly, after that we expect the plugin to work
				expect( $( '#ai1ec_start-time-input' ) ).toHaveValue( '15:30' );
				expect( $( '#ai1ec_end-time-input' ) ).toHaveValue( '16:30' );
				expect( $( '#ai1ec_start-date-input' ) ).toHaveValue( '19/6/2012' );
				expect( $( '#ai1ec_end-date-input' ) ).toHaveValue( '19/6/2012' );
			} );
			it( "Should show /hide the calendar after clicking on the field / icon", function() {
				expect( $( '#widgetCalendar' ) ).toHaveCss( { 'height' : '0px' } );
				$( '#ai1ec_exclude_date_label' ).click();
				// Start the animation
				waits( 50 );
				runs( function() {
					// it just has the height sety to 0 so jQuery consider this to be always visible
					expect( $( '#widgetCalendar' ) ).not.toHaveCss( { 'height' : '0px' } );
					// Click on it again
					$( '#ai1ec_exclude_date_label' ).click();
				} );
				// Wait for the animation to finish
				waits( 500 );
				runs( function() {
					expect( $( '#widgetCalendar' ) ).toHaveCss( { 'height' : '0px' } );
				} );
			} );
			it( "Should make an ajax call and block the UI when clicking the 'repeat' checkbox. We will test blockUI only with this test because all the calls are equal", function() {
				var block_ui_spy = spyOn( $, 'blockUI' );
				var post_spy = spyOn( $, 'post' );
				// Set the attr checked to true because the checkbox would become checked when the click event has finished
				$( '#ai1ec_repeat' ).attr( 'checked', true );
				// Trigger the evnt handler
				$( '#ai1ec_repeat' ).click();
				var data_block_ui = {
					message: '<div class="ai1ec-repeat-box-loading"></div>',
					css: {
						width: '358px',
						border: '0',
						background: 'transparent',
						cursor: 'normal'
					}
				};
				var data_post = {
					action: 'ai1ec_get_repeat_box',
					repeat: 1,
					post_id: '2144'
				};

				expect( block_ui_spy ).toHaveBeenCalledWith( data_block_ui );
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data_post, jasmine.any(Function), 'json' );
			} );
			it( "Should make an ajax call when clicking the 'exclude' checkbox", function() {
				// we set the block ui anyway otherwise the true blockUI is called
				var block_ui_spy = spyOn( $, 'blockUI' );
				var post_spy = spyOn( $, 'post' );
				// Set the attr checked to true because the checkbox would become checked when the click event has finished
				$( '#ai1ec_exclude' ).attr( 'checked', true );
				// Trigger the evnt handler
				$( '#ai1ec_exclude' ).click();
				var data_post = {
						action: 'ai1ec_get_repeat_box',
						repeat: 0,
						post_id: '2144'
				};
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data_post, jasmine.any(Function), 'json' );
			} );
			it( "It should make an ajax call when clicking on the exclude rule text", function() {
				// we set the block ui anyway otherwise the true blockUI is called
				var block_ui_spy = spyOn( $, 'blockUI' );
				var post_spy = spyOn( $, 'post' );
				$( '#ai1ec_exclude_text > a' ).click();
				var data_post = {
						action: 'ai1ec_get_repeat_box',
						repeat: 0,
						post_id: '2144'
				};
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data_post, jasmine.any(Function), 'json' );
			} );
			it( "It should make an ajax call when clicking on the repeat rule text", function() {
				// we set the block ui anyway otherwise the true blockUI is called
				var block_ui_spy = spyOn( $, 'blockUI' );
				var post_spy = spyOn( $, 'post' );
				$( '#ai1ec_repeat_text > a' ).click();
				var data_post = {
						action: 'ai1ec_get_repeat_box',
						repeat: 1,
						post_id: '2144'
				};
				expect( post_spy ).toHaveBeenCalledWith( ajaxurl, data_post, jasmine.any(Function), 'json' );
			} );
			describe( "Test the functionality of the repeat / exclude rules modal", function() {
				var data_second_post,
				    post_spy;
				// Set some action to-do before each test
				beforeEach( function() {
					var ajax_response = {
						"error" : false,
						"message" : "<ul class=\"ai1ec_repeat_tabs\">\r\n  <li><a href=\"#ai1ec_daily_content\" id=\"ai1ec_daily_tab\" class=\"ai1ec_tab ai1ec_active\">Daily<\/a><\/li>\r\n  <li><a href=\"#ai1ec_weekly_content\" id=\"ai1ec_weekly_tab\" class=\"ai1ec_tab\">Weekly<\/a><\/li>\r\n  <li><a href=\"#ai1ec_monthly_content\" id=\"ai1ec_monthly_tab\" class=\"ai1ec_tab\">Monthly<\/a><\/li>\r\n  <li><a href=\"#ai1ec_yearly_content\" id=\"ai1ec_yearly_tab\" class=\"ai1ec_tab\">Yearly<\/a><\/li>\r\n<\/ul>\r\n<div style=\"clear:both;\"><\/div>\r\n<div id=\"ai1ec_daily_content\" class=\"ai1ec_tab_content\" title=\"daily\">\r\n  <div class=\"ai1ec_repeat_centered_content\">\r\n  <label for=\"ai1ec_daily_count\">\r\n\t  Every:\r\n  <\/label>\r\n  \t\t\t<input type=\"range\" name=\"ai1ec_daily_count\" id=\"ai1ec_daily_count\" min=\"1\" max=\"365\"\r\n\t\t\t\tvalue=\"1\" \/>\r\n\t\tday(s)<\/div>  <div id=\"ai1ec_repeat_tab_append\">\r\n    <div id=\"ai1ec_ending_box\" class=\"ai1ec_repeat_centered_content\">\r\n  \t\t<div id=\"ai1ec_end_holder\">\r\n  \t\t  <label for=\"ai1ec_end\">\r\n  \t\t\t\tEnd:\r\n  \t\t\t<\/label>\r\n  \t\t\t \t\t<select name=\"ai1ec_end\" id=\"ai1ec_end\">\r\n\t\t\t\t\t\t\t<option value=\"0\" >\r\n\t\t\t\t\tNever\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"1\" >\r\n\t\t\t\t\tAfter\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"2\" >\r\n\t\t\t\t\tOn date\t\t\t\t<\/option>\r\n\t\t\t\t\t<\/select>\r\n\t\t  \t\t<\/div>\r\n  \t\t<div style=\"clear:both;\"><\/div>\r\n  \t\t<div id=\"ai1ec_count_holder\">\r\n  \t\t  <label for=\"ai1ec_count\">\r\n  \t\t\t\tEnding after:\r\n  \t\t\t<\/label>\r\n  \t\t\t\t\t\t<input type=\"range\" name=\"ai1ec_count\" id=\"ai1ec_count\" min=\"1\" max=\"365\"\r\n\t\t\t\tvalue=\"100\" \/>\r\n\t\ttimes  \t\t<\/div>\r\n  \t\t<div style=\"clear:both;\"><\/div>\r\n  \t\t<div id=\"ai1ec_until_holder\">\r\n  \t\t  <label for=\"ai1ec_until-date-input\">\r\n  \t\t\t\tOn date:\r\n  \t\t\t<\/label>\r\n  \t\t\t<input type=\"text\" class=\"ai1ec-date-input\" id=\"ai1ec_until-date-input\" \/>\r\n  \t\t\t<input type=\"hidden\" name=\"ai1ec_until_time\" id=\"ai1ec_until-time\" value=\"1340313594\" \/>\r\n  \t\t<\/div>\r\n  \t\t<div style=\"clear:both;\"><\/div>\r\n  \t<\/div>\r\n  \t<div id=\"ai1ec_apply_button_holder\">\r\n      <input type=\"button\" name=\"ai1ec_none_button\" value=\"Apply\" class=\"ai1ec_repeat_apply button button-highlighted\" \/>\r\n      <a href=\"#ai1ec_cancel\" class=\"ai1ec_repeat_cancel\">Cancel<\/a>\r\n    <\/div>\r\n    <div style=\"clear:both;\"><\/div>\r\n  <\/div>\r\n  <div style=\"clear:both;\"><\/div>\r\n<\/div>\r\n<div id=\"ai1ec_weekly_content\" class=\"ai1ec_tab_content\" title=\"weekly\">\r\n  <div class=\"ai1ec_repeat_centered_content\">\r\n  <label for=\"ai1ec_weekly_count\">\r\n\t  Every:\r\n  <\/label>\r\n  \t\t\t<input type=\"range\" name=\"ai1ec_weekly_count\" id=\"ai1ec_weekly_count\" min=\"1\" max=\"52\"\r\n\t\t\t\tvalue=\"1\" \/>\r\n\t\tweek(s)  <label>On:<\/label>\r\n  <div style=\"clear: both;\"><\/div>\r\n  \t\t<ul class=\"ai1ec_date_select ai1ec_weekly_date_select\" id=\"ai1ec_weekly_date_select\">\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tM\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_MO\" value=\"MO\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tT\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_TU\" value=\"TU\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tW\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_WE\" value=\"WE\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tT\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_TH\" value=\"TH\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tF\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_FR\" value=\"FR\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tS\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_SA\" value=\"SA\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tS\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select_SU\" value=\"SU\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t<\/ul>\r\n\t\t<input type=\"hidden\" name=\"ai1ec_weekly_date_select\" value=\"\" \/>\r\n\t\t<\/div><\/div>\r\n<div id=\"ai1ec_monthly_content\" class=\"ai1ec_tab_content\" title=\"monthly\">\r\n  <div class=\"ai1ec_repeat_centered_content\">\r\n  <label for=\"ai1ec_monthly_count\">\r\n\t  Every:\r\n  <\/label>\r\n  \t\t\t<input type=\"range\" name=\"ai1ec_monthly_count\" id=\"ai1ec_monthly_count\" min=\"1\" max=\"12\"\r\n\t\t\t\tvalue=\"1\" \/>\r\n\t\tmonth(s)  <div class=\"ai1ec_repeat_monthly_type\">\r\n\t<input type=\"radio\" name=\"ai1ec_monthly_type\" id=\"ai1ec_monthly_type_bymonthday\" value=\"bymonthday\" checked=\"1\" \/>\r\n\t<label for=\"ai1ec_monthly_type_bymonthday\">\r\n\t  On day of the month\t<\/label>\r\n\t<input type=\"radio\" name=\"ai1ec_monthly_type\" id=\"ai1ec_monthly_type_byday\" value=\"byday\" \/>\r\n\t<label for=\"ai1ec_monthly_type_byday\">\r\n\t  On day of the week\t<\/label>\r\n  <\/div>\r\n  <div style=\"clear:both;\"><\/div>\r\n  <div id=\"ai1c_repeat_monthly_bymonthday\">\r\n  \t\t\t<ul class=\"ai1ec_date_select ai1ec_montly_date_select\" id=\"ai1ec_montly_date_select\">\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t1\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_1\" value=\"1\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t2\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_2\" value=\"2\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t3\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_3\" value=\"3\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t4\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_4\" value=\"4\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t5\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_5\" value=\"5\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t6\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_6\" value=\"6\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t7\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_7\" value=\"7\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t8\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_8\" value=\"8\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t9\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_9\" value=\"9\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t10\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_10\" value=\"10\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t11\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_11\" value=\"11\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t12\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_12\" value=\"12\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t13\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_13\" value=\"13\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t14\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_14\" value=\"14\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t15\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_15\" value=\"15\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t16\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_16\" value=\"16\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t17\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_17\" value=\"17\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t18\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_18\" value=\"18\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t19\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_19\" value=\"19\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t20\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_20\" value=\"20\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t21\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_21\" value=\"21\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t22\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_22\" value=\"22\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t23\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_23\" value=\"23\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t24\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_24\" value=\"24\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t25\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_25\" value=\"25\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t26\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_26\" value=\"26\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t27\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_27\" value=\"27\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t28\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_28\" value=\"28\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t29\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_29\" value=\"29\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t30\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_30\" value=\"30\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t31\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select_31\" value=\"31\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t<\/ul>\r\n\t\t<input type=\"hidden\" name=\"ai1ec_montly_date_select\" value=\"\" \/>\r\n\t\t  <\/div>\r\n  <div id=\"ai1c_repeat_monthly_byday\">\r\n\t<label for=\"ai1ec_monthly_type_byday\">\r\n\t  Every\t<\/label>\r\n  \t\t\t<select name=\"ai1ec_monthly_byday_num\" id=\"ai1ec_monthly_byday_num\">\r\n\t\t\t\t\t\t\t<option value=\"1\" >\r\n\t\t\t\t\t1st\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"2\" >\r\n\t\t\t\t\t2nd\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"3\" >\r\n\t\t\t\t\t3rd\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"4\" >\r\n\t\t\t\t\t4th\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"5\" >\r\n\t\t\t\t\t5th\t\t\t\t<\/option>\r\n\t\t\t\t\t<\/select>\r\n\t\t  \t\t\t<select name=\"ai1ec_monthly_byday_weekday\" id=\"ai1ec_monthly_byday_weekday\">\r\n\t\t\t\t\t\t\t<option value=\"MO\" >\r\n\t\t\t\t\tMonday\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"TU\" >\r\n\t\t\t\t\tTuesday\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"WE\" >\r\n\t\t\t\t\tWednesday\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"TH\" >\r\n\t\t\t\t\tThursday\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"FR\" >\r\n\t\t\t\t\tFriday\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"SA\" >\r\n\t\t\t\t\tSaturday\t\t\t\t<\/option>\r\n\t\t\t\t\t\t\t<option value=\"SU\" >\r\n\t\t\t\t\tSunday\t\t\t\t<\/option>\r\n\t\t\t\t\t<\/select>\r\n\t\t  <\/div>\r\n<\/div><\/div>\r\n<div id=\"ai1ec_yearly_content\" class=\"ai1ec_tab_content\" title=\"yearly\">\r\n  <div class=\"ai1ec_repeat_centered_content\">\r\n  <label for=\"ai1ec_yearly_count\">\r\n\t  Every:\r\n  <\/label>\r\n  \t\t\t<input type=\"range\" name=\"ai1ec_yearly_count\" id=\"ai1ec_yearly_count\" min=\"1\" max=\"10\"\r\n\t\t\t\tvalue=\"1\" \/>\r\n\t\tyear(s)  <label for=\"ai1ec_yearly_date_select\">\r\n\t  In:\r\n  <\/label>\r\n  <div style=\"clear: both;\"><\/div>\r\n  \t\t<ul class=\"ai1ec_date_select ai1ec_yearly_date_select\" id=\"ai1ec_yearly_date_select\">\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tJan\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_1\" value=\"1\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tFeb\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_2\" value=\"2\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tMar\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_3\" value=\"3\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tApr\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_4\" value=\"4\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tMay\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_5\" value=\"5\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tJun\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_6\" value=\"6\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tJul\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_7\" value=\"7\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tAug\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_8\" value=\"8\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tSep\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_9\" value=\"9\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tOct\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_10\" value=\"10\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tNov\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_11\" value=\"11\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\tDec\t\t\t\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select_12\" value=\"12\" \/>\r\n\t\t\t\t<\/li>\r\n\t\t\t\t\t<\/ul>\r\n\t\t<input type=\"hidden\" name=\"ai1ec_yearly_date_select\" value=\"\" \/>\r\n\t\t<\/div><\/div>\r\n<input type=\"hidden\" id=\"ai1ec_is_box_repeat\" value=\"1\" \/>\r\n<div style=\"clear:both;\"><\/div>",
						"repeat" : 1
					};
					post_spy = spyOn( $, 'post' ).andCallFake( function( data, url, success ) {
						success( ajax_response );
					} );
					data_second_post = {
							action: 'ai1ec_rrule_to_text'
					};
					$( '#ai1ec_repeat' ).attr( 'checked', true );
					// Trigger the evnt handler
					$( '#ai1ec_repeat' ).click();
					waitsFor( function() {
						return $( 'div.blockUI' ).length > 0;
					} );
				} );
				// Set some action to do after each test
				afterEach( function() {
					$.unblockUI();
				} );
				it( "Should uncheck the checked button when clicking on the cancel button", function() {
					runs( function() {
						$( 'a.ai1ec_repeat_cancel' ).click();
					} );
					waitsFor( function() {
						return $( 'div.blockUI' ).length === 0;
					} );
					runs( function() {
						expect( $( '#ai1ec_repeat' ) ).not.toHaveAttr( 'checked' );
					} );
				} );
				it( "Should have a select which allows the user to choose the end for repetition / exclusion", function() {
					runs( function() {
						expect( $( '#ai1ec_end' ) ).toHaveValue( 0 );
						// Set the value to After
						$( '#ai1ec_end' ).val( 1 );
						$( '#ai1ec_end' ).trigger( "change" );
					} );
					waits( 500 );
					runs( function() {
						expect( $( '#ai1ec_count_holder' ) ).toBeVisible();
						$( '#ai1ec_end' ).val( 2 );
						$( '#ai1ec_end' ).trigger( "change" );
					} );
					waits( 500 );
					runs( function() {
						expect( $( '#ai1ec_until_holder' ) ).toBeVisible();
					} );
				} );
				describe( "Test the functionality of clicking on the 'Apply' button", function() {
					it( "Test with daily and 'never' end", function() {
						$( '#ai1ec_daily_count' ).val( 4 );
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = 'FREQ=DAILY;INTERVAL=4;';
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );
					} );
					it( "Test with daily and 'after' end", function() {
						$( '#ai1ec_daily_count' ).val( 4 );
						// Set the value of the select to After
						$( '#ai1ec_end' ).val( 1 );
						$( '#ai1ec_end' ).trigger( "change" );
						// Set the count to 100
						$( '#ai1ec_count' ).val( 100 );
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = "FREQ=DAILY;INTERVAL=4;COUNT=100;";
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );
					} );
					it( "Test with daily and 'until' end", function() {
						$( '#ai1ec_daily_count' ).val( 4 );
						// Set the value of the select to After
						$( '#ai1ec_end' ).val( 2 );
						$( '#ai1ec_end' ).trigger( "change" );
						// Set the until field to 22/06
						$( '#ai1ec_until-date-input' ).val( '22/06/2012' );
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = "FREQ=DAILY;INTERVAL=4;UNTIL=20120622T235959Z;";
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );
					} );
					it( "Test with weekly and 'never' end", function() {
						// Select the "Weekly" tab
						$( '#ai1ec_weekly_tab' ).click();
						// Click on the first day
						$( '#ai1ec_weekly_date_select li:first' ).click();
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = "FREQ=WEEKLY;WKST=MO;BYDAY=MO;";
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );
					} );
					it( "test with monthly, 'on day of the month'  and 'never' end", function() {
						// Select the "Monthly" tab
						$( '#ai1ec_monthly_tab' ).click();
						// Click on the first day
						$( '#ai1ec_montly_date_select li:first' ).click();
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = "FREQ=MONTHLY;BYMONTHDAY=1;";
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );

					} );
					it( "test with monthly, 'on day of the week' and 'never' end", function() {
						// Select the "Monthly" tab
						$( '#ai1ec_monthly_tab' ).click();
						// Click on the checkbox 'on day of the week'
						$( '#ai1ec_monthly_type_byday' ).click();
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = "FREQ=MONTHLY;BYDAY=1MO;";
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );
					} );
					it( "test with yearly, 'and 'never' end", function() {
						// Select the "Monthly" tab
						$( '#ai1ec_yearly_tab' ).click();
						// Click on the first day
						$( '#ai1ec_yearly_date_select li:first' ).click();
						$( 'input[name=ai1ec_none_button]' ).click();
						data_second_post.rrule = "FREQ=YEARLY;BYMONTH=1;";
						expect( post_spy.mostRecentCall.args ).toEqual( [ ajaxurl, data_second_post, jasmine.any(Function), 'json' ] );
					} );
				} );
			} );
		} );
	} );
} );
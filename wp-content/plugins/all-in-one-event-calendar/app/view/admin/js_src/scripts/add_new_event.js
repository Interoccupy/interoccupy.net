define(
	[
		'jquery',
		'domReady',
		'ai1ec_config',
		'scripts/add_new_event/event_location/gmaps_helper',
		'scripts/add_new_event/event_location/input_coordinates_event_handlers',
		'scripts/add_new_event/event_location/input_coordinates_utility_functions',
		'scripts/add_new_event/event_date_time/date_time_event_handlers',
		'scripts/add_new_event/facebook_export',
		'external_libs/jquery.calendrical_timespan',
		'external_libs/jquery.inputdate',
		'external_libs/jquery.tools',
		'external_libs/jquery.blockui',
		'external_libs/ai1ec_datepicker',
		'external_libs/bootstrap_collapse'
	],
	function (
		$,
		domReady,
		ai1ec_config,
		gmaps_helper,
		input_coordinates_event_handlers,
		input_utility_functions,
		date_time_event_handlers,
		facebook_export,
		calendrical_functions
	) {
	"use strict"; // jshint ;_;

	var init_date_time = function() {

		var now = new Date( ai1ec_config.now * 1000 );

		/**
		* Timespan plugin setup
		*/
		// Initialize timespan plugin on our date/time inputs.
		var data = {
			allday           : '#ai1ec_all_day_event',
			start_date_input : '#ai1ec_start-date-input',
			start_time_input : '#ai1ec_start-time-input',
			start_time       : '#ai1ec_start-time',
			end_date_input   : '#ai1ec_end-date-input',
			end_time_input   : '#ai1ec_end-time-input',
			end_time         : '#ai1ec_end-time',
			date_format      : ai1ec_config.date_format,
			month_names      : ai1ec_config.month_names,
			day_names        : ai1ec_config.day_names,
			week_start_day   : ai1ec_config.week_start_day,
			twentyfour_hour  : ai1ec_config.twentyfour_hour,
			now              : now
		};
		$.timespan( data );
		// Retrieve the dates saved in the hidden field
		var exdate  = $( "#ai1ec_exdate" ).val();

		// This variable holds the dates that must be selected in the datepicker.
		var dp_date = null;
		var _clear_dp = false;
		var _day;
		if( exdate.length >= 8 ) {
			dp_date = [];
			var _span_html = [];
			$.each( exdate.split( ',' ), function( i, v ) {
				var _date = v.slice( 0, 8 );
				var _year = _date.substr( 0, 4 );
				var _month = _date.substr( 4, 2 );
				_day = _date.substr( 6, 2 );

				_month = _month.charAt(0) === '0' ? ( '0' + ( parseInt( _month.charAt( 1 ), 10 ) - 1 ) ) : ( parseInt( _month, 10 ) - 1 );

				dp_date.push( new Date( _year, _month, _day ) );
				_span_html.push( 
					calendrical_functions.formatDate( 
						new Date( _year, _month, _day ),
						ai1ec_config.date_format,
						true
					)
				);
			});

			$( '#widgetField span:first' ).html( _span_html.join( ', ' ) );
		} else {
			// Set as default date shown today
			dp_date = new Date( ai1ec_config.now * 1000 );
			_clear_dp = true;
		}

		$( '#widgetCalendar' ).DatePicker({
			flat: true,
			calendars: 3,
			mode: 'multiple',
			start: 1,
			date: dp_date,
			onChange: function( formated ) {
				formated = formated.toString();
				if( formated.length >= 8 ) {
					// save the date in your hidden field
					var exdate = '';
					var formatted_date = [];
					$.each( formated.split( ',' ), function( i, v ) {
						formatted_date.push( calendrical_functions.formatDate( new Date( v ), ai1ec_config.date_format ) );
						exdate += v.replace( /-/g, '' ) + 'T000000Z,';
					});
					$( '#widgetField span' ).html( formatted_date.join( ', ' ) );
					exdate = exdate.slice( 0, exdate.length - 1 );
					$( "#ai1ec_exdate" ).val( exdate );
				} else {
					$( "#ai1ec_exdate" ).val( '' );
				}
			}
		});
		if( _clear_dp ) {
			$( '#widgetCalendar' ).DatePickerClear();
		}
		$( '#widgetCalendar div.datepicker' ).css( 'position', 'absolute' );
	};

	/**
	 * Add a hook into Bootstrap collapse for accordions for proper overflow
	 * behaviour when open.
	 */
	var init_collapsibles = function() {
		$( '.accordion-body' ).on( 'hide', function() {
			$( this ).removeClass( 'ai1ec-overflow-visible' );
		} );
		$( '.accordion-body' ).on( 'shown', function() {
			var $el = $( this );
			window.setTimeout(
				function() { $el.addClass( 'ai1ec-overflow-visible' ); },
				350
			);
		} );
	};

	/**
	 * Perform all initialization functions required on the page.
	 */
	var init = function() {
		init_date_time();

		// We load gMaps here so that we can start acting on the DOM as soon as possibe.
		// All initialization is done in the callback.
		require( ['libs/gmaps' ], function( gMapsLoader ) {
			gMapsLoader( gmaps_helper.init_gmaps );
		} );
	};

	/**
	 * Validate the form when clicking Publish/Update.
	 *
	 * @param  object e jQuery event object
	 */
	var validate_form = function( e ) {
		// Validate geolocation coordinates.
		if ( input_utility_functions.ai1ec_check_lat_long_fields_filled_when_publishing_event( e ) === true ) {
			// Convert commas to dots
			input_utility_functions.ai1ec_convert_commas_to_dots_for_coordinates();
			// Check that fields are ok and there are no errors
			input_utility_functions.ai1ec_check_lat_long_ok_for_search( e );
		}

		// Validate URL fields.
		$( '#ai1ec_ticket_url, #ai1ec_contact_url' ).each( function () {
			var url = this.value;
			if ( '' !== url ) {
				var urlPattern = /(http|https):\/\//;
				if ( ! urlPattern.test( url ) ) {
					window.alert( ai1ec_config.url_not_valid );
					e.preventDefault();
					// Just in case, hide the ajax spinner and remove the disabled status
					$( e.target ).removeClass( 'button-primary-disabled' );
					$( e.target ).siblings( '#ajax-loading' ).css( 'visibility', 'hidden' );
				}
			}
		} );
	};

	/**
	 * Attach event handlers to page.
	 */
	var attach_event_handlers = function() {
		// Toggle the visibility of google map on checkbox click
		$( '#ai1ec_google_map' ).click( input_coordinates_event_handlers.toggle_visibility_of_google_map_on_click );
		// Hide / Show the coordinates table when clicking the checkbox
		$( '#ai1ec_input_coordinates' ).change( input_coordinates_event_handlers.toggle_visibility_of_coordinate_fields_on_click );
		// Validate fields when clicking Publish
		$( '#publish, #ai1ec_bottom_publish' ).click( validate_form );
		// on blur, update the map if both coordinates are set
		$( 'input.coordinates' ).blur ( input_coordinates_event_handlers.update_map_from_coordinates_on_blur );
		// ==============================================
		// = EVENT HANDLERS FOR EVENT DATE AND TIME BOX =
		// ==============================================
		// Show different fields for the "ends" clause in the modal
		$( document ).on( 'change', '#ai1ec_end', date_time_event_handlers.show_end_fields );
		// If the extra publish button is present handle it's click
		$( '#ai1ec_bottom_publish' ).on( 'click', date_time_event_handlers.trigger_publish );
		// Handle clicking on tabs when the modal is open
		$( document ).on( 'click', '.ai1ec_tab', date_time_event_handlers.handle_click_on_tab_modal );
		// Handle click on the Apply button of the modal
		$( document ).on( 'click', '.ai1ec_repeat_apply', date_time_event_handlers.handle_click_on_apply_button );
		// Handle click on the cancel button of the modal
		$( document ).on( 'click', 'a.ai1ec_repeat_cancel', date_time_event_handlers.handle_click_on_cancel_modal );
		// Handle click on the cancel button of the modal
		$( document ).on( 'click', '#ai1ec_monthly_type_bymonthday, #ai1ec_monthly_type_byday', date_time_event_handlers.handle_checkbox_monthly_tab_modal );
		// initialize showing / hiding the calendars
		$( '#widgetField > a, #widgetField > span, #ai1ec_exclude_date_label' ).on( 'click', date_time_event_handlers.handle_animation_of_calendar_widget );
		$( document ).on( 'click', '#ai1ec_weekly_date_select > li,#ai1ec_montly_date_select > li,#ai1ec_yearly_date_select > li', date_time_event_handlers.handle_click_on_day_month_in_modal );
		// Attach event on the facebook export checkbox
		$( '#ai1ec_facebook_export' ).click( facebook_export.open_modal_when_user_chooses_to_unpublish_event );
		$( 'body' ).on( 'click', '.remove, .keep', facebook_export.add_hidden_field_when_user_click_remove_in_modal );
		// Attach pseudo handler function. These functions are kind of wrappers around other functions
		// i left them as i found them.
		date_time_event_handlers.execute_pseudo_handlers();
	};

	/**
	 * Place Event Details meta box below title, rather than below description.
	 */
	var reposition_meta_box = function() {
		$( '#ai1ec_event' )
			.insertAfter( '#titlediv' );
		$( '#post' ).addClass( 'ai1ec-visible' );
	};

	var start = function() {
		// Initialize the page. We do this before domReady so we start loading other
		// dependencies as soon as possible.
		init();
		domReady( function() {
			init_collapsibles();
			// Reposition event details meta box.
			reposition_meta_box();
			// Attach the event handlers
			attach_event_handlers();
		} );
	};

	return {
		start: start
	};
} );

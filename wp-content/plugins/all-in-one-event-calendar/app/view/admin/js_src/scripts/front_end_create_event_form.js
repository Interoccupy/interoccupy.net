define(
	[
		"jquery",
		"scripts/add_new_event/event_location/gmaps_helper",
		"libs/categories_select",
		"libs/tags_select",
		"libs/ajax_fileupload",
		"ai1ec_config",
		"external_libs/moment",
		"libs/recaptcha",
		"external_libs/jquery.placeholder",
		"external_libs/bootstrap_datepicker",
		"external_libs/bootstrap_timepicker",
		"external_libs/bootstrap_collapse",
		"external_libs/bootstrap_alert",
		"external_libs/bootstrap_fileupload",
		"external_libs/jquery.scrollTo"
	],
	function(
		$,
		gmaps_helper,
		categories_select,
		tags_select,
		ajax_fileupload,
		ai1ec_config,
		moment,
		recaptcha
	) {
	"use strict"; // jshint ;_;

	var $form = $( '.ai1ec-create-event-form' ),
	    $form_container = $form.parent(),
	    $spinner = $form_container.siblings( '.ai1ec-loading' );

	/**
	 * Initialize form.
	 */
	var init = function() {
		// Only load interactive gMaps code if address autocomplete is enabled.
		if ( ! ai1ec_config.disable_autocompletion ) {
			// We load gMaps here so that we can start acting on the DOM as soon as
			// possibe. All initialization is done in the callback.
			require( ['libs/gmaps'], function( gMapsLoader ) {
				gMapsLoader( gmaps_helper.init_gmaps );
			} );
		}

		// Initialize placeholder polyfill for IE9.
		$( 'input, textarea', $form ).placeholder();

		// Initialize datepickers.
		$( '.ai1ec-datepicker', $form )
			.datepicker( {
				autoclose: true,
				todayHighlight: true,
			} )
			// Wrap each datepicker in div.timely to avoid polluting global namespace.
			.each( function() {
				$( this ).data( 'datepicker' ).picker.wrapAll( '<div class="timely">' );
			} );

		// Note: Timepickers are initialized on first focus (in event handlers).

		// Initialize Category & Tag widgets.
		categories_select.init( $form );
		tags_select.init( $form );

		// Initialize reCAPTCHA (check is done to make sure it hasn't already been
		// initialized).
		recaptcha.init_recaptcha( $form );

		// Enable alert dismissal.
		$( '.alert', $form ).alert();

		// Focus first interactive input after a delay of 350 ms (after modal has
		// slid down).
		window.setTimeout(
			function() {
				$( ':input[type!="hidden"]:first', $form ).focus();
			},
			350
		);
	};

	/**
	 * Called when the form had an error and it needs the user's attention again.
	 * Brings attention to alert and reloads CAPTCHA.
	 */
	var retry_form = function() {
		if ( $( '#recaptcha_response_field', $form ).length &&
		     typeof Recaptcha !== 'undefined' ) {
			// Fetch new CAPTCHA challenge.
			Recaptcha.reload();
		}
		$( '.modal-body', $form ).scrollTo( '.alert:first', 1000 );
	}


	/**
	 * Event handler for timepicker's first focus. Initialize widget.
	 */
	var init_timepicker = function() {
		var $this = $( this );
		if ( typeof $this.data( 'timepicker' ) === 'undefined' ) {
			$this
				.timepicker( {
					showMeridian: $this.data( 'showMeridian' ),
					showInputs: false,
					defaultTime: $this.val() === '' ? 'current' : 'value'
				} )
				// Wrap timepicker in div.timely to avoid polluting global namespace.
				.data( 'timepicker' ).$widget.wrapAll( '<div class="timely">' );
		}
	};

	/**
	 * Handle toggling of "Add time" checkbox.
	 */
	var handle_has_time_checkbox = function() {
		if ( this.checked ) {
			$( '#ai1ec-end-time-input-wrap', $form ).collapse( 'show' );
			$( '#ai1ec-has-end-time ~ .ai1ec-without-time', $form ).hide();
			$( '#ai1ec-has-end-time ~ .ai1ec-with-time', $form ).show();
		}
		else {
			$( '#ai1ec-end-time-input-wrap', $form ).collapse( 'hide' );
			$( '#ai1ec-has-end-time ~ .ai1ec-without-time', $form ).show();
			$( '#ai1ec-has-end-time ~ .ai1ec-with-time', $form ).hide();
		}
		set_disabled_fields();
	};

	/**
	 * Returns whether the current form state would be a valid submission.
	 *
	 * @return {boolean}      True if the form fields pass validation
	 */
	var is_form_valid = function() {
		var has_end_time = $( '#ai1ec-has-end-time', $form )[0].checked;

		// Submit button disabled state.
		var $required = $(
			'#ai1ec-event-title, ' +
			'#ai1ec-start-date-input, ' +
			'#recaptcha_response_field',
			$form
		);
		if ( has_end_time ) {
			$required.add( $( '#ai1ec-end-date-input', $form ) );
		}

		var valid = true;
		$( '.alert-error', $form ).addClass( 'hide' );
		$required.each( function() {
			var $field = $( this );
			if ( $field.val() == '' ) {
				valid = false;
				// Add field name to alert message.
				$( '.ai1ec-missing-field', $form )
					.find( 'em' )
						.text( $field.attr( 'placeholder' ) )
						.end()
					.removeClass( 'hide' );
				$field.focus();
				return false;
			}
		} );

		return valid;
	};

	/**
	 * Calculate UNIX timestamps of currently entered date/time and save to form
	 * data storage. If start timestamp has changed since the last time,
	 * update the end time to preserve duration between start & end. If end time
	 * is before start time, set it to the start time.
	 */
	var update_timestamps = function() {
		var $start_date_input = $( '#ai1ec-start-date-input', $form ),
		    $end_date_input = $( '#ai1ec-end-date-input', $form ),
		    $start_time_input = $( '#ai1ec-start-time-input', $form ),
		    $end_time_input = $( '#ai1ec-end-time-input', $form ),
		    has_time = $( '#ai1ec-has-time', $form )[0].checked,
		    start_date = $start_date_input.val(),
		    start_time = $start_time_input.val(),
		    end_date = $end_date_input.val(),
		    end_time = $end_time_input.val(),
		    start = '',
		    end = '',
		    date_format,
		    time_format,
		    datetime_format,
		    prev_start = $form.data( 'ai1ecStartTime' ),
		    prev_end = $form.data( 'ai1ecEndTime' ),
		    update_end_widgets = false;

		// Normalize default values.
		if ( typeof prev_start === 'undefined' ) {
			prev_start = '';
		}
		if ( typeof prev_end === 'undefined' ) {
			prev_end = '';
		}

		// Build moment.js-compatible format strings.
		date_format = $start_date_input.data( 'dateFormat' ).toUpperCase();
		if ( $start_time_input.data( 'showMeridian' ) ) {
			time_format = 'hh:mm A'; // 12-hour time component
		} else {
			time_format = 'HH:mm';   // 24-hour time component
		}
		datetime_format = date_format + ' ' + time_format;

		// Parse start date/time.
		if ( start_date !== '' ) {
			if ( has_time && start_time !== '' ) {
				start = moment( start_date + ' ' + start_time, datetime_format );
			} else {
				start = moment( start_date, date_format );
			}
			start = start.unix();
		}

		// Find out if the new start timestamp is different from the previous start
		// timestamp. If so, then set end timestamp to the previous difference
		// between start and end times.
		if ( start !== '' &&
		     ( prev_start === '' || parseInt( prev_start ) !== start ) ) {

			// Provide default values to previous start/end timestamps.
			if ( prev_start === '' ) {
				prev_start = start;
			} else {
				prev_start = parseInt( prev_start );
			}
			if ( prev_end === '' ) {
				prev_end = prev_start;
			} else {
				prev_end = parseInt( prev_end );
			}

			// Calculate new end timestamp.
			end = start + prev_end - prev_start;
			update_end_widgets = true;
		}
		// Else parse end date/time.
		else if ( end_date !== '' ) {
			if ( has_time && end_time !== '' ) {
				end = moment( end_date + ' ' + end_time, datetime_format );
			} else {
				end = moment( end_date, date_format );
			}
			end = end.unix();

			// End time cannot be before start time. Update widgets if this is so.
			if ( end < start ) {
				end = start;
				update_end_widgets = true;
			}
		}

		if ( update_end_widgets ) {
			// Update end date/time widgets.
			var end_moment = moment.unix( end );
			$end_date_input
				.val( end_moment.format( date_format ) )
				.datepicker( 'update' )
				.datepicker( 'setStartDate', start_date );
			if ( has_time  && start_time !== '' ) {
				// Only update end time if start time was provided.
				$end_time_input.val( end_moment.format( time_format ) );
				var tp = $end_time_input.data( 'timepicker' );
				if ( typeof tp !== 'undefined' ) {
					tp.updateFromElementVal();
				}
			}
		}

		$form.data( 'ai1ecStartTime', start );
		$form.data( 'ai1ecEndTime', end );
	};

	/**
	 * Set enabled/disabled state of various form fields depending on current
	 * checkbox state.
	 */
	var set_disabled_fields = function() {
		// Date/time field disabled state.
		var has_time = $( '#ai1ec-has-time', $form )[0].checked,
		    has_end_time = $( '#ai1ec-has-end-time', $form )[0].checked;

		$( '#ai1ec-start-time-input', $form ).attr( 'disabled', ! has_time );
		$( '#ai1ec-end-date-input', $form ).attr( 'disabled', ! has_end_time );
		$( '#ai1ec-end-time-input', $form ).attr( 'disabled',
			! ( has_time && has_end_time ) );

		// Update stored UNIX timestamp values based on new disabled state.
		update_timestamps();
	};

	/**
	 * Handles the Submit Event button click.
	 */
	var handle_form_submission = function( e ) {
		e.preventDefault();
		if ( ! is_form_valid() ) {
			retry_form();
			return;
		}

		// Process stored timestamps before placing them into hidden fields.
		var start_time = $form.data( 'ai1ecStartTime' ),
		    end_time = $form.data( 'ai1ecEndTime' );

		var has_time =
			$( '#ai1ec-has-time', $form )[0].checked &&
			$( '#ai1ec-start-time-input', $form ).val() !== '';
		var has_end_time = $( '#ai1ec-has-end-time', $form )[0].checked;
		if ( ! has_time ) {
			// For all-day events without end date: default to start date.
			if ( ! has_end_time ) {
				end_time = start_time;
			}
			// Add 1 day to end date timestamp to span 1 full day.
			end_time = moment.unix( end_time ).add( 'd', 1 ).unix();
			// No longer an instantaneous event.
			has_end_time = true;
		}
		// For non-all-day events without end time, clear end date timestamp
		// (genuinely instantaneous events).
		else if ( ! has_end_time ) {
			end_time = '';
		}

		// Save processed timestamp data into hidden fields.
		$( '#ai1ec-start-time', $form ).val( start_time );
		$( '#ai1ec-end-time', $form ).val( end_time );

		// Set all-day/instant event fields to what the event creation form
		// processor expects.
		$( '#ai1ec-all-day-event', $form ).val( has_time ? '' : '1' );
		$( '#ai1ec-instant-event', $form ).val( has_end_time ? '' : '1' );

		$spinner.addClass( 'show' );
		ajax_fileupload.post(
			$form,
			'xml-json',
			function( data ) {
				// Display alert if there is a problem.
				if ( data.error ) {
					// Add field name to alert message.
					$( '.ai1ec-submit-error', $form )
						.text( data.message )
						.removeClass( 'hide' );
					// Fetch new CAPTCHA challenge.
					retry_form();
				}
				// Else display the new page.
				else {
					$form_container.html( data.html );
				}
				$spinner.removeClass( 'show' );
			}
		);
	};

	/**
	 * Handle "Post Another" button click. Reload fresh form and re-initialize it.
	 */
	var handle_post_another = function( e ) {
		e.preventDefault();
		$spinner.addClass( 'show' );
		$form_container.load(
			ai1ec_config.ajax_url + '?action=ai1ec_front_end_create_event_form',
			function() {
				$form = $( '.ai1ec-create-event-form', $form_container );
				init();
				$spinner.removeClass( 'show' );
			}
		);
	};

	/**
	 * Attach all event handlers.
	 */
	var attach_event_handlers = function() {
		// Synchronize time input collapsibles.
		$form_container.on( 'click', '#ai1ec-has-time', handle_has_time_checkbox );

		// Synchronize end date disabled with checkbox.
		$form_container.on( 'click', '#ai1ec-has-end-time', set_disabled_fields );

		// Initialize timepickers only on first focus to provide default time value
		// if empty.
		$form_container.on( 'focus', '.ai1ec-timepicker', init_timepicker );

		// Update values of hidden date/time timestamp fields.
		$form_container.on( 'changeDate', '.ai1ec-datepicker', function() {
			update_timestamps();
			// Enable checkboxes now that a start date has been chosen.
			$( '#ai1ec-has-time, #ai1ec-has-end-time', $form )
				.removeAttr( 'disabled' );
		} );
		$form_container.on( 'change', '.ai1ec-timepicker', update_timestamps );

		// Synchronize Show Map disabled state with collapsible.
		$form_container.on( 'keyup', '#ai1ec_address', function() {
			var $checkbox = $( '#ai1ec-google-map', $form );
			if ( this.value === '' ) {
				if ( $checkbox[0].checked ) {
					$checkbox.click();
				}
				$checkbox.attr( 'disabled', 'disabled' );
			}
			else {
				$checkbox.removeAttr( 'disabled' );
			}
		} );

		// Handle form submission.
		$form_container.on( 'click', '.ai1ec-submit', handle_form_submission );

		// Handle "Post Another" button.
		$form_container.on( 'click', '.ai1ec-post-another', handle_post_another );
	};

	/**
	 * Initialize event creation form.
	 */
	var start = function() {
		init();
		attach_event_handlers();
	};

	var init_recaptcha = function() {
		recaptcha.init_recaptcha( $form );
	};

	return {
		start: start,
		init_recaptcha : init_recaptcha
	};
} );

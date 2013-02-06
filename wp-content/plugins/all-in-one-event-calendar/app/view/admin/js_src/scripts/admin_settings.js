define(
	[
		"jquery",
		"domReady",
		"ai1ec_config",
		"libs/collapse_helper",
		"external_libs/bootstrap_tab",
		"external_libs/bootstrap_dropdown",
		"external_libs/bootstrap_datepicker",
		"external_libs/bootstrap_tooltip",
		"external_libs/jquery_cookie"
	],
	function( $, domReady, ai1ec_config ) {
	"use strict";

	var remove_feeds_postbox_if_all_values_are_empty = function() {
		var remove = true;
		$( '#ai1ec-plugins-settings input:text' ).each( function() {
			if ( this.value !== '' ) {
				remove = false;
			}
		} );
		if ( remove === true ) {
			$( '#ai1ec-plugins-settings' ).remove();
		}
	};

	/**
	 * Destroys and reinitializes the datepicker on the given element with the
	 * given data map to assign to the element before initializing the new
	 * datepicker. The previous date value is preserved.
	 *
	 * @param  {object} $el  jQuery object of element datepicker is attached to
	 * @param  {array}  data Data map to assign to $el before calling .datepicker()
	 */
	var reset_datepicker = function( $el, data ) {
		// Save the old date from the datepicker.
		var cur_date = false;
		if ( $el.val() !== '' ) {
			cur_date = $el.data( 'datepicker' ).date;
		}
		// Destroy the datepicker.
		var dp = $el.data( 'datepicker' );
		dp.hide();
		dp.picker.remove();
		$el.removeData( 'datepicker' );
		// Reinitialize datepicker to use the new pattern, and restore the date.
		$el.data( data ).datepicker();
		dp = $el.data( 'datepicker' );
		if ( cur_date !== false ) {
			dp.date = cur_date;
			dp.setValue();
		}
	}

	/**
	 * Activate given tab. If null, activate first tab.
	 *
	 * @param  {string} active_tab Value of tab's href attribute
	 */
	var activate_saved_tab = function( active_tab ) {
		if ( active_tab === null ){
			// Activate the first tab
			$( '#ai1ec-general-settings ul.nav-tabs a:first' ).tab( 'show' );
		} else {
			// Activate the correct tab
			$( '#ai1ec-general-settings ul.nav-tabs a[href="' + active_tab + '"]' )
				.tab( 'show' );
		}
	};

	/**
	 * Event handler when tab is clicked; saves chosen tab to cookie.
	 *
	 * @param  {string} active_tab Value of tab's href attribute
	 */
	var handle_set_tab_cookie = function( e ) {
		var active = $( this ).attr( 'href' );
		$.cookie( 'ai1ec_general_settings_active_tab', active );
	};

	/**
	 * Toggle disabled states on certain settings depending on value of other
	 * settings.
	 */
	var set_disabled_states = function() {
		var show_create_button = $( '#show_create_event_button' )[0].checked,
		    $show_front_end_form = $( '#show_front_end_create_form' ),
		    show_front_end_form = $show_front_end_form[0].checked,
		    $allow_anonymous = $( '#allow_anonymous_submissions' ),
		    allow_anonymous = $allow_anonymous[0].checked;

		$show_front_end_form.attr( 'disabled',
			! show_create_button
		);
		$allow_anonymous.attr( 'disabled',
			! ( show_create_button && show_front_end_form )
		);
		$( '#allow_anonymous_uploads' ).attr( 'disabled',
			! ( show_create_button && show_front_end_form && allow_anonymous )
		);
	}

	/**
	 * Initialize the license status indicator with API call.
	 */
	var init_license_status = function() {
	};

	// Perform DOM ready tasks.
	domReady( function() {
		init_license_status();

		// Handle saving/loading of active tab.
		activate_saved_tab( $.cookie( 'ai1ec_general_settings_active_tab' ) );
		$( document ).on(
			'click',
			'#ai1ec-general-settings .nav-tabs a[data-toggle="tab"]',
			handle_set_tab_cookie
		);

		// Initialize datepicker and have it respond to changes in format settings.
		var $exact_date = $('#exact_date');
		$exact_date.datepicker();

		// Apply the new date pattern when "Calendar default start date" is changed.
		$( document ).on( 'change', '#input_date_format', function() {
			var pattern = $( 'option:selected', this ).data( 'pattern' );
			reset_datepicker( $exact_date, { dateFormat: pattern } );
		} );
		// Change the week start day in the picker.
		$( document ).on( 'change', '#week_start_day', function() {
			var week_start_day = $( this ).val();
			reset_datepicker( $exact_date, { dateWeekstart: week_start_day } );
		} );

		remove_feeds_postbox_if_all_values_are_empty();

		// On clicking a .toggle-view
		$( document ).on( "click", '.ai1ec-admin-view-settings .toggle-view',  function () {
			// check to see if there are any siblings that are checked
			var is_one_box_checked = $( '.ai1ec-admin-view-settings .toggle-view:checked' ).length === 0;
			// check if this view is selected as the default via radio button
			var is_selected_default = $( this ).parents( 'tr' ).find( '.toggle-default-view:checked' ).length === 1;
			// if either is true, prevent :checked state change
			if ( is_one_box_checked === true || is_selected_default === true ) {
				return false;
			}
		} );

		// When clicking a radio button to select a default view
		$( document ).on( "click", '.ai1ec-admin-view-settings .toggle-default-view', function () {
			// Automatically set the associated checkbox property to :checked
			$( this ).parents( 'tr' ).find( '.toggle-view' ).prop( 'checked', true );
		} );

		// Enable/disable show_front_end_create_form checkbox based on status of
		// show_create_event_button checkbox.
		$( document ).on(
			'click ready',
			'#show_create_event_button, #show_front_end_create_form, #allow_anonymous_submissions',
			set_disabled_states
		);

		$( '#show_create_event_button' ).trigger( 'ready' );
	} );
} );

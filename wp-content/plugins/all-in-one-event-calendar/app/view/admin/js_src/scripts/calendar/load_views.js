/*global History: true */
define(
	[
		"jquery",
		"scripts/calendar/print",
		"scripts/calendar/posterboard_view",
		"scripts/calendar/agenda_view",
		"scripts/calendar/month_view",
		"libs/frontend_utils",
		"ai1ec_calendar",
		"ai1ec_config",
		"scripts/common_scripts/frontend/common_frontend",
		"external_libs/jquery_history",
		"external_libs/jquery.tablescroller",
		"external_libs/jquery.scrollTo",
		"external_libs/bootstrap_datepicker"
	],
	function(
		$,
		print_functions,
		posterboard_view,
		agenda_view,
		month_view,
		frontend_utils,
		ai1ec_calendar,
		ai1ec_config,
		common_frontend
	) {
	"use strict"; // jshint ;_;

	/**
	 * function initialize_view
	 *
	 * General initialization function to execute whenever any view is loaded
	 * (this is also called at the end of load_view()).
	 */
	var initialize_view = function() {

		// Get the dropdown menu link of the active view.
		var $selected_view = $('#ai1ec-view-dropdown .dropdown-menu .active a');

		// Make week view table scrollable
		$( 'table.ai1ec-week-view-original' ).tableScroll( { height: 400, containerClass: 'ai1ec-week-view ai1ec-popover-boundary' } );
		$( 'table.ai1ec-oneday-view-original' ).tableScroll( { height: 400, containerClass: 'ai1ec-oneday-view ai1ec-popover-boundary' } );

		if( $( '.ai1ec-week-view' ).length || $( '.ai1ec-oneday-view' ).length ) {
			// If no active event, then in week view, scroll down to 6am.
			$( '.ai1ec-oneday-view .tablescroll_wrapper, .ai1ec-week-view .tablescroll_wrapper' ).scrollTo( '.ai1ec-hour-marker:eq(6)' );
		}

		// If in month view, extend multiday events.
		if ( $( '.ai1ec-month-view .ai1ec-multiday' ).length ) {
			month_view.extend_multiday_events();
		}

		// If in posterboard view, initialize masonry.
		if ( $( '.ai1ec-posterboard-view' ).length ) {
			posterboard_view.resize_masonry();
		}
	};

	/**
	 * Do any cleanup required before currently displayed view is replaced with
	 * a newly retrieved view.
	 */
	var destroy_view = function() {
		// Destroy any datepicker before loading new view.
		var dp = $( '.ai1ec-minical-trigger' ).data( 'datepicker' );
		if ( typeof dp !== 'undefined' ) {
			dp.picker.parent( '.timely' ).remove();
		}
		// Destroy any visible tooltips or popovers.
		$( '.tooltip.in, .ai1ec-popup' ).remove();
	}

	/**
	 * Load a calendar view represented by the given hash value.
	 *
	 * @param {string} hash The hash string requesting a calendar view
	 */
	var load_view = function( hash, type ) {
		// Reveal loader behind view
		$('#ai1ec-calendar-view-loading').fadeIn( 'fast' );
		$('#ai1ec-calendar-view').fadeTo( 'fast', 0.3,
			// After loader is visible, fetch new content
			function() {
				var query = {
						request_type: type,
						ai1ec_doing_ajax : true
				};
				// Fetch AJAX result
				$.ajax( {
					url : hash,
					dataType: type,
					data: query,
					method : 'get',
					success: function( data ) {
						// Do required cleanup of existing view.
						destroy_view();

						// Views Dropdown
						if( typeof data.views_dropdown === 'string' ) {
							$( '.ai1ec-views-dropdown' ).replaceWith( data.views_dropdown );
						}
						// Update categories
						if( typeof data.categories === 'string' ) {
							$( '.ai1ec-category-filter' ).replaceWith( data.categories );
						}
						// Tags
						if( typeof data.tags === 'string' ) {
							$( '.ai1ec-tag-filter' ).replaceWith( data.tags );
						}
						// And the "Subscribe buttons"
						if( typeof data.subscribe_buttons === 'string' ) {
							$( '.ai1ec-subscribe-container' ).replaceWith( data.subscribe_buttons );
						}

						// Animate vertical height of container between HTML replacement
						var $container = $('#ai1ec-calendar-view-container');
						$container.height( $container.height() );
						var new_height =
							$('#ai1ec-calendar-view')
								.html( data.html )
								.height();
						$container.animate( { height: new_height }, { complete: function() {
							// Restore height to automatic upon animation completion for
							// proper page layout.
							$container.height( 'auto' );
						} } );

						// Hide loader
						$('#ai1ec-calendar-view-loading').fadeOut( 'fast' );
						$('#ai1ec-calendar-view').fadeTo( 'fast', 1.0 );
						// Do any general view initialization after loading
						initialize_view();
					}
				}
				);
			}
		);
	};

	// When the state changes, load the corresponding view
	var handle_state_change = function( e ) {
		var state = History.getState();
		load_view( state.url, 'json' );
	};

	// Handle loading the correct view when clicking on a link
	var handle_click_on_link_to_load_view = function( e ) {
		var $el = $( this );

		e.preventDefault();

		if( $el.data( 'type' ) === 'json' ) {
			History.pushState( null, null, $el.attr( 'href' ) );
		} else {
			load_view( $el.attr( 'href' ), 'jsonp' );
		}
	};

	/**
	 * Click of minical trigger button. If not initialized, initialize datepicker.
	 * Then show datepicker.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_minical_trigger = function( e ) {
		var $el = $( this );

		e.preventDefault();

		if ( typeof $el.data( 'datepicker' ) === 'undefined' ) {
			// Initialize this view's minical datepicker.
			$el.datepicker( {
					todayBtn: 'linked',
					todayHighlight: true,
					templateOverrides: 'headTemplate contTemplate',
					headTemplate:
						'<thead><tr class="datepicker-btn-group">' +
							'<th class="prev"><div class="dp-btn"><i class="icon-arrow-left"/></div></th>' +
							'<th colspan="5" class="switch"><div class="dp-btn"></div></th>' +
							'<th class="next"><div class="dp-btn"><i class="icon-arrow-right"/></div></th>' +
						'</tr></thead>',
					contTemplate: '<tbody><tr><td colspan="7" class="grid-picker"></td></tr></tbody>'
				} );

			// Extend Datepicker behaviour without modifying the plugin.
			var dp = $el.data( 'datepicker' );
			// Wrap datepicker in div.timely to avoid polluting global namespace, and
			// flag as right-aligned.
			dp.picker
				.wrapAll( '<div class="timely" />' )
				.addClass( 'ai1ec-right-aligned' );
			// Replace the place() method so that it is right-aligned to trigger.
			var place_orig = dp.place;
			dp.place = function() {
				place_orig.call( this );
				var $el = this.component ? this.component : this.element;
				var offset = $el.offset();
				this.picker.css( {
					left: 'auto',
					right: $( document ).width() - offset.left - $el.outerWidth()
				} );
			};
		}

		$el.datepicker( 'show' );
	}

	/**
	 * Handle loading the correct view when selecting date from the datepicker.
	 * Destroy datepicker first.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_minical_change_date = function( e ) {
		var url,
		    $el = $( this );

		$el.datepicker( 'hide' );

		// Get URL template, and date, replacing '/' with '-' to be URL-friendly.
		url = $el.data( 'href' );
		date = $el.data( 'date' ).replace( /\//g, '-' );
		url = url.replace( '__DATE__', date );
		if ( $el.data( 'type' ) === 'json' ) {
			History.pushState( null, null, url );
		} else {
			load_view( url, 'jsonp' );
		}
	}

	// Handle clearing filter
	var clear_filters = function() {
		History.pushState( null, null, $( this ).data( 'href' ) );
	};

	return {
		initialize_view                    : initialize_view,
		handle_click_on_link_to_load_view  : handle_click_on_link_to_load_view,
		handle_minical_trigger             : handle_minical_trigger,
		handle_minical_change_date         : handle_minical_change_date,
		clear_filters                      : clear_filters,
		handle_state_change                : handle_state_change
	};
});

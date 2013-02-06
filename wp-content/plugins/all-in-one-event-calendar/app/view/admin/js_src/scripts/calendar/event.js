define(
		[
		 "jquery",
		 "external_libs/jquery.scrollTo"
		 ],
		 function( $ ) {
	"use strict"; // jshint ;_;

	var load_event_through_jsonp = function( e ) {
		e.preventDefault();
		// Remove popovers
		$( 'div.popover' ).remove();
		var type = 'jsonp';
		var timely_div = $( this ).closest( '.timely-calendar' );
		var query = {
				request_type: type,
				ai1ec_doing_ajax : true,
				ai1ec : create_ai1ec_to_send( timely_div )
		};

		// Fetch AJAX result
		$.ajax( {
			url : $( this ).attr( 'href' ) ,
			dataType: type,
			data: query,
			method : 'get',
			success: function( data ) {
				// Use the closest container relative to the target
				$( e.target ).closest( '#ai1ec-calendar-view' ).html( data.html );
				// Update the back to calendar button with the
				var href = $( '.ai1ec-calendar-link' ).attr( 'href' );
				var timely_action = $( e.target ).closest( '.timely-calendar' ).data( 'action' );
				if( timely_action ) {
					href = href + 'action:' + timely_action + '/';
				}
				// Scroll to the relative div top to bring the event details into focus.
				$.scrollTo( timely_div, 1000,
					{
						offset: {
							left: 0,
							top: -100
						}
					}
				);

				// Start up requirejs
				require(
						[
						 'scripts/event'
						 ],
						 function( page ) {
							page.start();
				} );
			}
		} );
	};
	var add_value_to_array_if_present_on_el = function( key, params, $el, skip_key ) {
		var camel_key = dashToCamel( key );
		var value = $el.data( camel_key );
		if ( value === undefined ) {
			return params;
		} else {
			if( skip_key ) {
				params.push( value );
			} else {
				params.push( key + ':' + value );
			}
			return params;
		}

	};

	/**
	 * Convert a string to camelcase
	 *
	 */
	var dashToCamel = function( str ) {
		return str.replace(/\W+(.)/g, function (x, chr) {
			return chr.toUpperCase();
		});
	};
	/**
	 * Creates the ai1ec variable to send to the server to filter the calendar
	 *
	 */
	var create_ai1ec_to_send = function( el ) {
		var $el = $( el );
		var params = [];
		params = add_value_to_array_if_present_on_el( 'action', params, $el );
		params = add_value_to_array_if_present_on_el( 'cat_ids', params, $el );
		params = add_value_to_array_if_present_on_el( 'tag_ids', params, $el );
		params = add_value_to_array_if_present_on_el( 'exact_date', params, $el );
		params = add_value_to_array_if_present_on_el( 'no_navigation', params, $el );
		return params.join( '|' );
	};
	return {
		load_event_through_jsonp : load_event_through_jsonp,
		create_ai1ec_to_send     : create_ai1ec_to_send
	};
} );

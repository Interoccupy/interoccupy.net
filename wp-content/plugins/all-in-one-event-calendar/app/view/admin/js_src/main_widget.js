require(
		[
		 "scripts/calendar",
		 "scripts/common_scripts/frontend/common_frontend",
		 "scripts/calendar/event",
		 "domReady",
		 "jquery",
		 "ai1ec_calendar"
		 ],
		function( page, common, event, domReady, $, config ) {
	"use strict"; // jshint ;_;

	var url = config.calendar_url;

	$( 'div.timely-calendar:first' ).on( 'click', '.ai1ec-load-event[data-type=jsonp]', event.load_event_through_jsonp );
	// If there are multiple divs load multiple calendars
	$( 'div.timely-calendar:first' ).each( function( i, el ) {
		var ai1ec = event.create_ai1ec_to_send( el );
		var data = {
			ai1ec_doing_ajax : true,
			request_type: 'jsonp',
			ai1ec : ai1ec
		};
		$.ajax( {
			url: url,
			dataType: 'jsonp',
			data: data,
			success: function( data ) {
				var $view_container = $( '<div/>', { id : 'ai1ec-calendar-view-container' } );
				var $timely = $( '<div/>', { 'class' : 'timely' } );
				var spinner = $( '<div/>', {
					id : 'ai1ec-calendar-view-loading',
					"class" : 'ai1ec-loading'
				} );
				var $view_div = $( '<div/>', { id : 'ai1ec-calendar-view' } );
				$view_div.append( data.html );
				$view_container.append( spinner ).append( $view_div );
				$timely.append( $view_container ).append( data.subscribe_buttons );
				$( el ).append( $timely );
				// the common library might be already loaded if we are embedding the calendar
				// In a wordpress page with our plugin installed.
				if( ! common.are_event_listeners_attached() ) {
					common.start();
				}
				page.start();
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				window.alert( "An error occurred while retrieving the calendar data." );
			}
		} );
	} );


} );

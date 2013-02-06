define(
		[
		 "jquery"
		 ],
		 function( $ ) {
	"use strict"; // jshint ;_;
	// *** Agenda view ***

	/**
	 * Callbacks for event expansion, collapse.
	 */
	var toggle_event = function() {
		$( this )
			// Find the parent li.ai1ec-event, toggle its class.
			.closest( '.ai1ec-event' )
				.toggleClass( 'ai1ec-expanded' )
				// Find the event summary and slideToggle it
				.find( '.ai1ec-event-summary' )
					.slideToggle( 300 );
	};
	var collapse_all = function() {
		$( '.ai1ec-expanded .ai1ec-event-toggle')
			.click();
	};

	var expand_all = function() {
		$( '.ai1ec-event:not(.ai1ec-expanded) .ai1ec-event-toggle')
			.click();
	};
	return {
		toggle_event   : toggle_event,
		collapse_all   : collapse_all,
		expand_all     : expand_all
	};
} );

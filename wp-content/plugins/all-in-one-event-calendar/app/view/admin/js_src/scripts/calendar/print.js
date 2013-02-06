/**
 * This module handles the print button behaviour.
 */
define(
	[
		"jquery"
	],
	function( $ ) {
	"use strict"; // jshint ;_;

	/**
	 * Handle clicks on the print button.
	 *
	 * @param {object} e jQuery Event object
	 */
	var handle_click_on_print_button = function( e ) {
		e.preventDefault();
		// get the calendar html
		var $body = $( 'body' )
		  , $html = $( 'html' )
		  , view = $( '#ai1ec-container' ).html()
		  , body = $body.html();
		// Remove all scripts tag otherwise they are reapplied when the
		// html is used.
		body = body.replace( /<script.*?>([\s\S]*?)<\/script>/gmi, '' );
		// Empty the page
		$body.empty();
		// Add the namespace to the body
		$body.addClass( 'timely' );
		// add the print class to the document
		$html.addClass( 'ai1ec-print' );
		// Attacch our calendar
		$body.html( view );
		// Disable clicking on title
		$( 'span' ).click( function() {
			return false;
		} );
		// Open the print screen
		window.print();
		// remove classes we added
		$body.removeClass( 'timely' );
		$html.removeClass( 'ai1ec-print' );
		// Attach back the body
		$body.html( body );
	};

	return {
		handle_click_on_print_button     : handle_click_on_print_button
	};
} );

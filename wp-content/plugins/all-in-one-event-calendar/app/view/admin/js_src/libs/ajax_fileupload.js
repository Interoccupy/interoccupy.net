define(
	[
		"jquery",
	],
	function( $ ) {
	"use strict"; // jshint ;_;

	/**
	 * Submits the given $form into a hidden iframe so that file uploads can be
	 * received by the server.
	 *
	 * @param  {object}   $form   jQuery object representing form to POST
	 * @param  {string}   type    Either 'json', 'xml-json', 'xml', or 'html' -
	 *                            the expected format of the HTTP response; if
	 *                            'json' or 'xml-json', data argument in success
	 *                            callback is the parsed JSON object; if 'xml' or
	 *                            'html', data argument is XML/HTML content parsed
	 *                            into a jQuery object.
	 * @param  {function} success Callback that receives 1 argument, data,
	 *                            containing the HTTP response of the submission.
	 */
	var post = function( $form, type, success ) {
		// Attempt to generate unique ID.
		var id = "ajax_fileupload" + ( new Date().getTime() );

		// Create hidden iframe and append to page.
		var $iframe = $( '<iframe name="' + id + '"/>' )
			.css( { border: 'none', width: 0, height: 0 } );
		$iframe.appendTo( 'body' );

		// Handle load event once.
		$iframe.one( 'load', function() {
			var data;
			// Return JS object if JSON format expected.
			// NOTE: Not typically used as it causes issues in Internet Explorer
			// (which prompts to download any file that has JSON content type), and
			// issues a warning in other browsers.
			if ( type === 'json' ) {
				data = $.parseJSON( $iframe.contents().text() );
			}
			// XML wrapper around JSON object.
			else if ( type === 'xml-json' ) {
				// Handle IE's wonderfully special and uniquely different way of
				// providing access to XML document data. So nice that MS give us the
				// chance to spend hours studying their curious departure from the norm.
				var doc;
				if ( typeof ( doc = $iframe[0].contentWindow.document.XMLDocument ) !==
				     'undefined' ) {
					data = $( doc ).text();
				}
				else {
					data = $iframe.contents().text();
				}
				data = $.parseJSON( data );
			}
			// Else return jQuery object containing returned XML/HTML structure.
			else {
				data = $iframe.contents().children();
			}

			// Pass to callback.
			success( data );

			// Clean up.
			$iframe.remove();
		} );

		// Prepare form.
		$form.attr( {
			target: id,
			method: 'post',
			enctype: 'multipart/form-data',
			encoding: 'multipart/form-data'
		} );

		$form.submit();
	};

	return {
		post: post
	};
} );

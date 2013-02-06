define(
		[
		 "jquery",
		 "ai1ec_config"
		 ],
		 function( $, ai1ec_config ) {
	"use strict"; // jshint ;_;

	var handle_click_on_submit_file = function( e ) {
		var filename = $( '#ai1ec_file_input' ).val();
		var textarea = $( '#ai1ec_upload_textarea' ).val();
		// Check if something was provided
		if( filename === '' && textarea === '' ) {
			e.preventDefault();
			alert( ai1ec_config.file_upload_required );
		} else {
			// If a filename was provided check if it's valid
			if( filename !== '' ) {
				var extension = filename.substr( filename.length - 4 , 4 );
				if( extension !== '.ics' && extension !== '.csv' ) {
					e.preventDefault();
					alert( ai1ec_config.file_upload_not_permitted );
				}
			}
		}
	};
	return {
		handle_click_on_submit_file : handle_click_on_submit_file
	};
} );
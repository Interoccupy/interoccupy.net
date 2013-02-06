define(
	[
		"jquery",
		"external_libs/select2"
	],
	function( $ ) {
	"use strict"; // jshint ;_;

	/**
	 * Initialize any tag selectors on the page. Limit search to $container
	 * parent element if provided.
	 *
	 * @param  {object} $container jQuery object representing parent container
	 */
	var init = function( $container ) {
		if ( typeof $container === 'undefined' ) {
			$container = $( document );
		}
		$( '.ai1ec-tags-selector', $container ).each( function() {
			var $this = $( this );
			$this
				.select2( {
					allowClear: true,
					tags: $this.data( 'ai1ecTags' ),
					tokenSeparators: [ ',' ]
				} );
		} );
	}

	return {
		init: init
	};
} );

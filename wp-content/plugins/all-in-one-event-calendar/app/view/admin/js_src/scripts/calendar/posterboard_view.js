define(
	[
	 "jquery",
	 "external_libs/jquery.masonry",
	],
	function( $, masonry ) {
	"use strict";

	// *** Posterboard view layout ***

	/**
	 * Initializes and/or reflows masonry on the active posterboard view (usually
	 * when the window has been resized, or after web fonts have been loaded).
	 */
	var resize_masonry = function() {

		var $container = $( '.ai1ec-posterboard-view' ),
		    $tiles = $( '> .ai1ec-event', $container ),
		    container_width,
		    col_width,
		    num_columns;

		// Don't reflow masonry if no posterboard view is available.
		if ( $container.length === 0 ) {
			return;
		}

		// Get new width of container & columns.
		container_width = $container.parent().width();
		// Min column width locally is col_width (a variable set in the ai1ec
		// settings by the global var $ai1ec_settings->posterboard_tile_min_width).
		col_width = $container.data( 'ai1ecTileMinWidth' );
		// We then stretch until the container width is filled.
		num_columns = Math.floor( container_width / col_width );
		// Don't create more columns than there are tiles.
		num_columns = Math.min( num_columns, $tiles.length );
		col_width = Math.floor( container_width / num_columns );

		// Reset width to auto to bypass masonry inline CSS.
		$container.css( 'width', 'auto' );

		// Size event tiles to desired column width.
		$tiles.width( col_width );

		// Configure masonry on the view.
		$container.imagesLoaded( function() {
			var $loading = $( '#ai1ec-calendar-view-loading' );
			$container
				.masonry({
					itemSelector: '.ai1ec-event',
					isFitWidth: true,
					isResizable: false, // We are handling resize events ourselves
					isAnimated: true,
					columnWidth: col_width,
					animationOptions: { easing: 'swing' }
				});
		});

		// Reload masonry; needed if it has already been initialized (most cases).
		$container.imagesLoaded( function() {
			$container.masonry( 'reload' );
		});
	};

	/**
	 * Reloads masonry on the active posterboard view, in case its contents have
	 * changed.
	 */
	var reload_masonry = function() {
		$( '.ai1ec-posterboard-view' ).masonry( 'reload' );
	};

	return {
		resize_masonry: resize_masonry,
		reload_masonry: reload_masonry
	};
} );

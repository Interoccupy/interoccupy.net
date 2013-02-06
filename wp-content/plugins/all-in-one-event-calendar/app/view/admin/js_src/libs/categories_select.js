define(
	[
		"jquery",
		"external_libs/select2"
	],
	function( $ ) {
	"use strict"; // jshint ;_;

	/**
	 * Formatter for selected event categories shown in the Select2 widget.
	 *
	 * @param  {object} category Passed by Select2 representing selected item
	 * @return {string}          Markup for formatted item
	 */
	var format_selection = function( option ) {
		var $option = $( option.element ),
				color = $option.data( 'color' ),
				description = $option.data( 'description' ),
				markup = '';

		if ( typeof color !== 'undefined' && color !== '' ) {
			markup += '<span class="ai1ec-category-color" style="background: ' +
				$option.data( 'color' ) + '"></span> ';
		}
		markup += option.text;
		markup = '<span title="' + description + '">' + markup + '</span>';

		return markup;
	};

	/**
	 * Formatter for event categories shown in the Select2 widget dropdown.
	 *
	 * @param  {object} category Passed by Select2 representing selected item
	 * @return {string}          Markup for formatted item
	 */
	var format_result = function( option ) {
		var $option = $( option.element ),
				color = $option.data( 'color' ),
				description = $option.data( 'description' ),
				markup = '';

		if ( typeof color !== 'undefined' && color !== '' ) {
			markup += '<span class="ai1ec-category-color" style="background: ' +
				$option.data( 'color' ) + '"></span> ';
		}
		else {
			markup += '<span class="ai1ec-category-color-empty"></span> ';
		}
		markup += option.text;
		markup = '<span title="' + description + '">' + markup + '</span>';

		return markup;
	};

	/**
	 * Initialize any category selectors on the page. Limit search to $container
	 * parent element if provided.
	 *
	 * @param  {object} $container jQuery object representing parent container
	 */
	var init = function( $container ) {
		if ( typeof $container === 'undefined' ) {
			$container = $( document );
		}
		$( '.ai1ec-categories-selector', $container )
			.select2( {
				allowClear: true,
				formatResult: format_result,
				formatSelection: format_selection
			} );
	}

	return {
		init: init
	};
} );

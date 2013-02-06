define(
	[
		"jquery",
		"scripts/calendar/posterboard_view"
	],
	function( $, posterboard_view ) {
	"use strict"; // jshint ;_;

	/**
	 * Handler for popover trigger: mouseenter.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_popover_over = function( e ) {
		var $this = $( this ),
				$pop_content = $this.next( '.ai1ec-popup' ),
				el_content_data, el_classes_data, popover_placement;

		// If no popover found, quit.
		if ( $pop_content.length == 0 ) {
			return;
		}

		el_content_data = $pop_content.html();
		el_classes_data = $pop_content.attr( 'class' );

		// Position popover to the left only if there's room for it within the
		// bounds of the view (popovers are 182 pixels wide, a product of padding
		// and inner width as defined in style.less).
		var $bounds = $this.closest( '#ai1ec-calendar-view' );
		if ( $bounds.length === 0 ) {
			$bounds = $( 'body' );
		}
		if ( $this.offset().left - $bounds.offset().left > 182 ) {
			popover_placement = 'left';
		} else {
			popover_placement = 'right';
		}

		$this.constrained_popover( {
			content: el_content_data,
			title: '',
			placement: popover_placement,
			trigger: 'manual',
			html: true,
			template: '<div class="timely popover ' + el_classes_data +
				'"><div class="arrow"></div><div class="popover-inner">' +
				'<div class="popover-content"><div></div></div></div></div></div>',
			container: '.ai1ec-popover-boundary'
		}).constrained_popover( 'show' );
	}

	/**
	 * Handler for popover trigger: mouseleave. Remove popup if entering an
	 * element that is not the popup.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_popover_out = function( e ) {
		var $el = $( e.toElement || e.relatedTarget );
		// If an ancestor of element being entered is not a popup, hide popover.
		if ( $el.closest( '.ai1ec-popup' ).length == 0 ) {
			$( this ).constrained_popover( 'hide' );
		}
	}

	/**
	 * Handler for popover; remove the popover on mouseleave of itself. Hide popup
	 * if entering an element that is not a tooltip.
	 * Also remove any visible tooltip if removing popup.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_popover_self_out = function( e ) {
		var $el = $( e.toElement || e.relatedTarget );
		// If an ancestor of element being entered is not a tooltip, hide popover.
		if ( $el.closest( '.tooltip' ).length == 0 ) {
			$( this ).remove();
			$( 'body > .tooltip' ).remove();
		}
	};

	/**
	 * Manually handle tooltip mouseenter.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_tooltip_over = function( e ) {
		var $this = $( this );
		var params = {
			'template': '<div class="timely tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
		};
		// For tooltips contained in popovers, we want total control over their
		// show/hide.
		var is_in_popover = $this.closest( '.ai1ec-popup' ).length;
		if ( is_in_popover ) {
			params.trigger = 'manual';
		}

		$this.tooltip( params );

		if ( is_in_popover ) {
			$this.tooltip( 'show' );
		}
	}

	/**
	 * Manually handle tooltip mouseleave. Do not hide if entering tooltip or
	 * tooltip triggering action.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_tooltip_out = function( e ) {
		var $el = $( e.toElement || e.relatedTarget );
		if ( $el.closest( '.tooltip' ).length == 0 ) {
			$( this ).tooltip( 'hide' );
		}
	}

	/**
	 * Handler for tooltip; remove the tooltip on mouseleave of itself, unless
	 * moving onto the tooltip trigger action. If moving onto an element that is
	 * not in a popup, hide any visible popup.
	 *
	 * @param  {object} e JS event object
	 */
	var handle_tooltip_self_out = function( e ) {
		var $el = $( e.toElement || e.relatedTarget );
		// If an ancestor of element being entered is not a tooltip trigger action,
		// hide tooltip.
		if ( $el.closest( '.ai1ec-tooltip-trigger' ).length == 0 ) {
			$( this ).remove();
		}
		// If an ancestor of element being entered is not a popup, hide any popup.
		if ( $el.closest( '.ai1ec-popup' ).length == 0 ) {
			$( 'body > .ai1ec-popup' ).remove();
		}
	};

	/**
	 * Handler for web font loader. Do any required actions when web fonts are
	 * loaded.
	 */
	var handle_fonts_loaded = function( e ) {
		posterboard_view.resize_masonry();
		if ( $( 'html' ).is( '#ie8' ) ) {
			//$( '[class^="icon-"]' ).css( 'zoom', '1' );
		}
	};

	return {
		handle_popover_over        : handle_popover_over,
		handle_popover_out         : handle_popover_out,
		handle_popover_self_out    : handle_popover_self_out,
		handle_tooltip_over        : handle_tooltip_over,
		handle_tooltip_out         : handle_tooltip_out,
		handle_tooltip_self_out    : handle_tooltip_self_out,
		handle_fonts_loaded        : handle_fonts_loaded
	};
} );

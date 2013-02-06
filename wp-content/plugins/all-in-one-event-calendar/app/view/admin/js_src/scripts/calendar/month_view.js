define(
		[
		 "jquery",
		 "external_libs/modernizr"
		 ],
		function( $, Modernizr ) {
	"use strict"; // jshint ;_;
	// *** Month view ***

	/**
	 * Extends day bars for multiday events.
	 */
	var extend_multiday_events = function() {
		var $days = $('.ai1ec-day');
		var daysFirstWeek = $( '.ai1ec-week:first .ai1ec-day' ).length;

		$('.ai1ec-month-view .ai1ec-multiday').each( function() {
			var container = this.parentNode;
			var elHeight = $(this).outerHeight( true );
			var endDay = parseInt( $(this).data( 'endDay' ), 10 );
			var $startEl = $( '.ai1ec-date', container );
			var startDay = parseInt( $startEl.text(), 10 );

			var nextMonthBar = $( this ).data( 'endTruncated' );
			if ( nextMonthBar ) {
				endDay = parseInt( $($days[$days.length - 1]).text(), 10 ) ;
			}

			var $evtContainer = $(this);
			var bgColor = $( '.ai1ec-event', $evtContainer )[0].style.backgroundColor;
			var curLine = 0;
			var deltaDays = endDay - startDay + 1;
			var daysLeft = deltaDays;
			var marginSize;

			// this is the variable used to count the number of days for the event
			var days = 0;

			$days.each( function( i ) {
				var $dayEl = $( '.ai1ec-date', this );
				var $td = $( this.parentNode );
				var cellNum = $td.index();
				var day = parseInt( $dayEl.text(), 10 );
				if ( day >= startDay && day <= endDay ) {
					if ( day === startDay ) {
						marginSize = parseInt( $dayEl.css( 'marginBottom' ), 10 ) + 16;
					}

					if ( curLine === 0 ) {
						// Extend initial event bar to the end of first (!) week.
						days++;
					}

					if ( cellNum === 0 && day > startDay && daysLeft !== 0 ) {
						// Clone the event as well as its associated popup
						var $clone = $evtContainer
							.next( '.ai1ec-popup' )
							.andSelf()
							.clone( false );
						$dayEl.parent().append( $clone );

						var $block = $clone.first();

						// Create a new spanning multiday bar. "ai1ec-multiday-bar" is used
						// for proper styling, while "ai1ec-multiday-clone" identifies the
						// clones so they can be removed when required.
						$block.addClass( 'ai1ec-multiday-bar ai1ec-multiday-clone' );

						$block
							.css({
								position: "absolute",
								left: '1px',
								top: parseInt( $dayEl.css( 'marginBottom' ), 10 ) + 13, // line height is 16px - 3px of initial margin
								backgroundColor: bgColor
							});

						// Check the days left, if they are more than 7 a new block is needed and we draw 7 days only
						var daysForThisBlock = ( daysLeft > 7 ) ? 7 : daysLeft;

						$block.css( 'width', create_percentual_width_from_days( daysForThisBlock ) );

						if ( daysLeft > 7 ) {
							$block.append( create_multiday_arrow( 1, bgColor ));
						}

						$block.append( create_multiday_arrow( 2, bgColor ));
					}

					// Keep constant margin (number of bars) during the first row.
					if ( curLine === 0 ) {
						$dayEl.css({ 'marginBottom': marginSize + 'px' });
					}
					// But need to reset it and append margins from the begining for
					// subsequent weeks.
					else {
						$dayEl.css({ 'marginBottom': '+=16px' });
					}

					daysLeft--;

					// If in the last column of the table and there are more days left,
					// increment curLine.
					if ( daysLeft > 0 && cellNum == 6 ) {
						curLine++;
					}
				}
			});
			// Adding "start arrow" to the end of multi-month bars.
			if ( nextMonthBar ) {
				var $lastBarPiece = $( '.' + $evtContainer[0].className.replace( /\s+/igm, '.' ) ).last();
				$lastBarPiece.append( create_multiday_arrow( 1, bgColor ));
			}

			$(this).css({
				position: 'absolute',
				top: $startEl.outerHeight( true ) - elHeight - 1 + 'px',
				left: '1px',
				width: create_percentual_width_from_days( days )
			});

			// Add an ending arrow to the initial event bar for multi-week events.
			if ( curLine > 0 ) {
				$(this).append( create_multiday_arrow( 1, bgColor ) );
			}
			// Add a starting arrow to the initial event bar for events starting in
			// previous month.
			if ( $(this).data( 'startTruncated' ) ) {
				$(this)
					.append( create_multiday_arrow( 2, bgColor ) )
					.addClass( 'ai1ec-multiday-bar' );
			}
		});
	};

	/**
	 * returns a string with the percentage to use as width for the specified number of days
	 *
	 * @param int days the number of days
	 *
	 */
	var create_percentual_width_from_days = function( days ) {
		var percent;
		switch ( days ) {
			case 1:
				percent = 97.5;
				break;
			case 2:
				percent = 198.7;
				break;
			case 3:
				percent = 300;
				break;
			case 4:
				percent = 401;
				break;
			case 5:
				if( $.browser.webkit || $.browser.opera ) {
					percent = 507;
				} else {
					percent = 503.4;
				}
				break;
			case 6:
				if( $.browser.webkit || $.browser.opera ) {
					percent = 608;
				} else {
					percent = 603.5;
				}
				break;
			case 7:
				if( $.browser.webkit || $.browser.opera ) {
					percent = 709;
				} else {
					percent = 705;
				}
				break;
		}
		return percent + '%';
	};

	/**
	 * Creates arrow for multiday bars.
	 *
	 * @param {int}    type  1 for ending arrow, 2 for starting arrow
	 * @param {string} color Color of the multiday event
	 */
	var create_multiday_arrow = function( type, color ) {
		var $arrow = $( '<div class="ai1ec-multiday-arrow' + type + '"></div>' );
		if ( type === 1 ) {
			$arrow.css({ borderLeftColor: color });
		} else {
			$arrow.css({ borderTopColor: color, borderRightColor: color, borderBottomColor: color });
		}
		return $arrow;
	};

	/**
	 * Trims date boxes for which there are too many listed events.
	 */
	/* NOT USED
	var truncate_month_view = function()
	{
		if( $( '.ai1ec-month-view' ).length )
		{
			// First undo any previous truncation
			revert_dropdowns();

			// Now set up truncation on any days with max visible events.
			$( '.ai1ec-month-view .ai1ec-day' ).each( function()
			{
				var max_visible = 5;
				var maxVisibleHeight = 5 * 16;
				var addDropdownContainer = -1;
				var $events = $( '.ai1ec-event-container', this );

				$events.each( function( i ) {
					if ( this.offsetTop >= maxVisibleHeight && addDropdownContainer === -1 ) {
						addDropdownContainer = ( i > 1 ? i - 1 : 0 );
					 }

				});

				if ( addDropdownContainer !== -1 ) {
					var container = document.createElement("div");
					container.className = "ai1ec-event-dropdown";

					$( container ).css({
						top: $events[addDropdownContainer].offsetTop,
						display: "none"
					});
					for ( var i = addDropdownContainer; i < $events.length; i++ ) {
						// Need to reset styles for events in dropdown.
						revert_multiday_bar( $events[i] );

						// Add an arrow for multiday events.
						if ( $( $events[i] ).hasClass( "ai1ec-multiday" ) ) {
							$( $events[i] ).append( create_multiday_arrow( 1, $events[i].style.backgroundColor ) );
						}
						$( container ).append( $events[i] );
					}

					// Scroll down button, and register mousedown.
					var $scroll_down = $( '<a href="#" class="ai1ec-scroll-down"></a>' );
					$scroll_down.bind( 'hover click', function () {
						$( container ).fadeIn( 100 );
						return false;
					});

					var $date = $( this ).find( ".ai1ec-date" );
					if ( parseInt( $date.css( "marginBottom" ), 10 ) > maxVisibleHeight ) {
						$date.css({ marginBottom: maxVisibleHeight - 15 + "px" });
						$( container ).css({
							top: maxVisibleHeight + "px"
						});
					}
					$( this ).append(container);
					$( this ).append($scroll_down);

					// Need additional button to close dropdown on touch devices
					if ( Modernizr.touch ) {
						// Scroll down button, and register mousedown
						var $scroll_up = $( '<a href="#" class="ai1ec-scroll-up"></a>' );
						$scroll_up.bind("click", function () {
							$( container ).fadeOut( 100 );
							return false;
						});
						$( container ).append($scroll_up);
					} else {
						$( container ).bind( 'mouseleave' ,function() {
							$( this ).fadeOut( 100 );
						});
					}
				}
			});
		}
	};*/

	return {
		extend_multiday_events: extend_multiday_events
	};

} );

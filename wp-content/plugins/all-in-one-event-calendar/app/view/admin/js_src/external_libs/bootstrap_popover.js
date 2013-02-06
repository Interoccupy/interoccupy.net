define(
    [
     "jquery",
     "external_libs/bootstrap_tooltip"
     ],
     function( $, domReady ) {
	/* ===========================================================
	 * bootstrap-popover.js v2.0.4
	 * http://twitter.github.com/bootstrap/javascript.html#popovers
	 * ===========================================================
	 * Copyright 2012 Twitter, Inc.
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 * http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 * =========================================================== */

if ( ! $.fn.popover ) {

    "use strict"; // jshint ;_;

 /* POPOVER PUBLIC CLASS DEFINITION
  * =============================== */

  var Popover = function ( element, options ) {
    this.init('popover', element, options)
  }


  /* NOTE: POPOVER EXTENDS BOOTSTRAP-TOOLTIP.js
     ========================================== */

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype, {

    constructor: Popover

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()
        , content = this.getContent()

      $tip.find('.popover-title')[this.isHTML(title) ? 'html' : 'text'](title)
      $tip.find('.popover-content > *')[this.isHTML(content) ? 'html' : 'text'](content)

      $tip.removeClass('fade top bottom left right in')
    }

  , hasContent: function () {
      return this.getTitle() || this.getContent()
    }

  , getContent: function () {
      var content
        , $e = this.$element
        , o = this.options

      content = $e.attr('data-content')
        || (typeof o.content == 'function' ? o.content.call($e[0]) :  o.content)

      return content
    }

  , tip: function () {
      if (!this.$tip) {
        this.$tip = $(this.options.template)
      }
      return this.$tip
    }

  })


 /* POPOVER PLUGIN DEFINITION
  * ======================= */

  $.fn.popover = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('popover')
        , options = typeof option == 'object' && option
      if (!data) $this.data('popover', (data = new Popover(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.popover.Constructor = Popover

  $.fn.popover.defaults = $.extend({} , $.fn.tooltip.defaults, {
    placement: 'right'
  , content: ''
  , template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
  })

}

if ( ! $.fn.constrained_popover ) {

	/* CONSTRAINED_POPOVER PUBLIC CLASS DEFINITION
	 * =========================================== */

	var ConstrainedPopover = function ( element, options ) {
		this.init('constrained_popover', element, options )
	};

	/* NOTE: CONSTRAINED_POPOVER EXTENDS BOOTSTRAP-POPOVER.js
	 ========================================== */

	ConstrainedPopover.prototype = $.extend( {}, $.fn.popover.Constructor.prototype, {

		constructor: ConstrainedPopover

	, show: function () {
	  var $tip
	    , inside
	    , pos
	    , newPos
	    , actualWidth
	    , actualHeight
	    , placement
	    , tp
	    , finalPos = {}

	  if (this.hasContent() && this.enabled) {
	    $tip = this.tip()
	    this.setContent()

	    if (this.options.animation) {
	      $tip.addClass('fade')
	    }

	    placement = typeof this.options.placement == 'function' ?
	      this.options.placement.call(this, $tip[0], this.$element[0]) :
	      this.options.placement

	    inside = /in/.test(placement)

	    $tip
	      .remove()
	      .css({ top: 0, left: 0, display: 'block' })
	      .appendTo(inside ? this.$element : document.body)

	    pos = this.getPosition(inside)

	    actualWidth = $tip[0].offsetWidth
	    actualHeight = $tip[0].offsetHeight

	    switch (inside ? placement.split(' ')[1] : placement) {
	      case 'left':
			newPos = this.defineBounds( pos )
			if ( typeof newPos.top === "undefined" ) {
				finalPos["top"] = pos.top + pos.height / 2 - actualHeight / 2
			} else {
				finalPos["top"] = newPos.top - actualHeight / 2
			}
			if ( typeof newPos.left === "undefined" ) {
				finalPos["left"] = pos.left - actualWidth
			} else {
				finalPos["left"] = newPos.left - actualWidth
			}
	        tp = { top: finalPos.top , left: finalPos.left }
	        break
	      case 'right':
			newPos = this.defineBounds( pos )
			if ( typeof newPos.top === "undefined" ) {
				finalPos["top"] = pos.top + pos.height / 2 - actualHeight / 2
			} else {
				finalPos["top"] = newPos.top - actualHeight / 2
			}
			if ( typeof newPos.left === "undefined" ) {
				finalPos["left"] = pos.left + pos.width
			} else {
				finalPos["left"] = newPos.left + pos.width
			}
	        tp = { top: finalPos.top , left: finalPos.left }
	        break
	    }

	    $tip
	      .css(tp)
	      .addClass(placement)
	      .addClass('in')
	  }
	}

	, defineBounds: function ( pos ) {
		var container
		, containerOffset
		, boundTop
		, boundLeft
		, boundBottom
		, boundRight
		, newPos = {}

		if ( $(this.options.container).length !== 0 ) {
			// verify there is no special "inner-container" with checkMultiContainer()
			container = this.checkMultiContainer()
		    containerOffset = container.offset()

		    boundTop = containerOffset.top
		    boundLeft = containerOffset.left
		    boundBottom = boundTop + container.height()
		    boundRight = boundLeft + container.width()

			// Constrain y-axis overflow
			if ( pos.top + ( pos.height / 2 ) < boundTop ) {
				newPos["top"] = boundTop
			}
			if ( pos.top + ( pos.height / 2 ) > boundBottom ) {
				newPos["top"] = boundBottom
			}
			// Constrain x-axis overflow
			if ( pos.left - ( pos.width / 2 ) < boundLeft ) {
				newPos["left"] = boundLeft
			}
			if ( pos.left - ( pos.width / 2 ) > boundRight ) {
				newPos["left"] = boundRight
			}
			return newPos
		}
		else {
			return false
		}
	}

	, checkMultiContainer: function () {
		var container
		, containerNum

		container = $( this.options.container )
		if ( container.length ) {
			if ( container.length > 1 ) {
				container = this.$element.closest( container )
			}
			return container
		} else {
			return
		}
	}

	})

	 /* CONSTRAINED_POPOVER PLUGIN DEFINITION
	  * ===================================== */

	$.fn.constrained_popover = function ( option ) {
		return this.each( function () {
			var $this = $(this)
		        , data = $this.data('constrained_popover')
		        , options = typeof option == 'object' && option
		      if (!data) $this.data('constrained_popover', (data = new ConstrainedPopover(this, options)))
		      if (typeof option == 'string') data[option]()
		})
	}

	$.fn.constrained_popover.Constructor = ConstrainedPopover

	$.fn.constrained_popover.defaults = $.extend({} , $.fn.popover.defaults, {
		container: ''
		, content: this.options
	})
} // END: CONSTRAINED_POPOVER

} );

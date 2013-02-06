define(
		[
		 "jquery",
		 "external_libs/jquery.autocomplete_geomod"
		 ],
		 function( $ ) {

$.fn.extend({
	geo_autocomplete: function( _geocoder, _options ) {
		options = $.extend({}, $.Autocompleter.defaults, {
			geocoder: _geocoder,
			mapwidth: 100,
			mapheight: 100,
			maptype: 'terrain',
			mapkey: 'ABQIAAAAbnvDoAoYOSW2iqoXiGTpYBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQNumU68AwGqjbSNF9YO8NokKst8w', // localhost
			mapsensor: false,
			parse: function(_results, _status, _query) {
				var _parsed = [];
				if (_results && _status && _status == 'OK') {
					$.each(_results, function(_key, _result) {
						if (_result.geometry && _result.geometry.viewport) {
							// place is first matching segment, or first segment
							var _place_parts = _result.formatted_address.split(',');
							var _place = _place_parts[0];
							$.each(_place_parts, function(_key, _part) {
								if (_part.toLowerCase().indexOf(_query.toLowerCase()) != -1) {
									_place = $.trim(_part);
									return false; // break
								}
							});
							_parsed.push({
								data: _result,
								value: _place,
								result: _place
							});
						}
					});
				}
				return _parsed;
			},
			formatItem: function(_data, _i, _n, _value) {
				var _src = 'http://maps.google.com/maps/api/staticmap?visible=' +
					_data.geometry.viewport.getSouthWest().toUrlValue() + '|' +
					_data.geometry.viewport.getNorthEast().toUrlValue() +
					'&size=' + options.mapwidth + 'x' + options.mapheight +
					'&maptype=' + options.maptype +
					'&key=' + options.mapkey +
					'&sensor=' + (options.mapsensor ? 'true' : 'false');
				var _place = _data.formatted_address.replace(/,/gi, ',<br/>');
				return '<img src="' + _src + '" width="' + options.mapwidth +
					'" height="' + options.mapheight + '" /> ' + _place +
					'<br clear="both"/>';
			}
		}, _options);

		// if highlight is set to false, replace it with a do-nothing function
		options.highlight = options.highlight || function(value) { return value; };

		// if the formatMatch option is not specified, then use formatItem for backwards compatibility
		options.formatMatch = options.formatMatch || options.formatItem;

		// Add class to hide results until restyled below.
		options.resultsClass = 'ai1ec-geo-ac-results-not-ready';

		return this.each( function() {
			// Schedule polling function the first time the form element is focused.
			// The polling function will check once a second if the results have been
			// shown, and if so, apply markup-based styling to it. Then the function
			// is cancelled.
			$( this ).one( 'focus', function() {
				var interval_id = setInterval(
					function() {
						var $results = $( '.ai1ec-geo-ac-results-not-ready' );
						if ( $results.length ) {
							$results
								.removeClass( 'ai1ec-geo-ac-results-not-ready' )
								.addClass( 'ai1ec-geo-ac-results' )
								.wrap( '<div class="timely"/>' )
								.children( 'ul' )
									.addClass( 'dropdown-menu' );
							clearInterval( interval_id );
						}
					},
					500
				);
			} );

			new $.Autocompleter( this, options );
		} );
	}
});

} );

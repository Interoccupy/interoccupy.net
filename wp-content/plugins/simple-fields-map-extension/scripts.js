

// like the überclass start starts it all
var simple_fields_googlemap = (function($) {

	var
		map_container_class = ".simple-fields-fieldtype-googlemap-map-container"
		;

	return {
		
		init: function(){
			this.load_map_script();

			// Listen for added field groups
			$(document.body).bind("field_group_added", function(e, response) {
				var $response = $(response);
				// check if the added field group has our field type in it
				var $this_field_type_fields = $response.find(".simple-fields-fieldgroups-field-type-googlemaps");
				if ($this_field_type_fields.length) {
					// Yes, the added html has our field type in it
					// So attach our maps object to it, ey!
					$this_field_type_fields.find(map_container_class).each(function(i, elm) {
						var oneMap = new simple_fields_googlemap_map($);
						oneMap.init(elm);
					});

				}
			});

		},
		
		load_map_script: function() {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "http://maps.google.com/maps/api/js?libraries=places&sensor=false&callback=simple_fields_googlemap.map_script_loaded";
			document.body.appendChild(script);
		},
		
		// Map script is loaded.
		// Now find all map containers and attach maps and stuff
		map_script_loaded: function() {
			$(map_container_class).each(function(i, elm) {
				var oneMap = new simple_fields_googlemap_map($);
				oneMap.init(elm);
		});
		}
				
	};

}(jQuery));

// class for each map
// not each field. each map.
var simple_fields_googlemap_map = (function($) {
	
	var
		map,
		map_container,
		startLatLng,
		metaboxDiv,
		marker,
		infowindow,
		autocomplete,
	
		addMarker = function() {

			marker = new google.maps.Marker({
				position: startLatLng,
				map: map,
				draggable: true
			});

			google.maps.event.addListener(marker, 'dragstart', function(event) {
				// Manual drag, so remove the extra info we got from place autocomplete
				metaboxDiv.find("input.simple-fields-field-googlemap-name").val("");
				metaboxDiv.find("input.simple-fields-field-googlemap-formatted_address").val("");
				metaboxDiv.find("input.simple-fields-field-googlemap-address_components").val("");
				metaboxDiv.find("span.simple-fields-field-googlemap-selected-formatted_address").text("");
				metaboxDiv.find("span.simple-fields-field-googlemap-selected-name").text("");
				infowindow.close();
			});

			google.maps.event.addListener(marker, 'position_changed', function(event) {
				
				var pos = marker.getPosition();
				metaboxDiv.find("input.simple-fields-field-googlemap-lat").val(pos.lat());
				metaboxDiv.find("input.simple-fields-field-googlemap-lng").val(pos.lng());

				metaboxDiv.find(".simple-fields-fieldtype-googlemap-selected-positions").show();
				
				// Output rounded coords on screen beause like 10 digits is just to noisy
				var coefficient = Math.pow(10, 4);
				var roundedLat = Math.round(pos.lat() * coefficient) / coefficient;
				var roundedLng = Math.round(pos.lng() * coefficient) / coefficient;
				metaboxDiv.find(".simple-fields-field-googlemap-selected-lat").text(roundedLat);
				metaboxDiv.find(".simple-fields-field-googlemap-selected-lng").text(roundedLng);
				
			});
						
			google.maps.event.addListener(marker, 'dragend', function(event) {
			});
		},
		
		init = function(elm) {

			mapContainer = jQuery(elm);
			metaboxDiv = mapContainer.closest("div.simple-fields-metabox-field");

			// Default locations
			var initLat = metaboxDiv.find("input.simple-fields-field-googlemap-lat-init-position");
			var initLng = metaboxDiv.find("input.simple-fields-field-googlemap-lng-init-position");
			
			// Saved locations
			var savedLat = metaboxDiv.find("input.simple-fields-field-googlemap-lat");
			var savedLng = metaboxDiv.find("input.simple-fields-field-googlemap-lng");
			
			// If saved location exists then map should be placed at that location
			// If not, check initLat is
			if (savedLat.val() && savedLng.val()) {
				startLatLng = new google.maps.LatLng(savedLat.val(), savedLng.val());
			} else if (initLat.val() && initLng.val()) {
				startLatLng = new google.maps.LatLng(initLat.val(), initLng.val());
			} else {
				// last resort: Stockholm!
				startLatLng = new google.maps.LatLng(58.7886, 16.9122);
			}
			
			// Get defaults from data attribute
			var mapTypeId = mapContainer.data("defaultmaptypeid");
			var zoomLevel = mapContainer.data("defaultzoomlevel");
			
			switch (mapTypeId) {
				case "ROADMAP":
					mapTypeId = google.maps.MapTypeId.ROADMAP;
					break;
				case "SATELLITE":
					mapTypeId = google.maps.MapTypeId.SATELLITE;
					break;
				case "HYBRID":
					mapTypeId = google.maps.MapTypeId.HYBRID;
					break;
				case "TERRAIN":
					mapTypeId = google.maps.MapTypeId.TERRAIN;
					break;
			}
			
			var mapOptions = {
				zoom: zoomLevel,
				navigationControl: true,
				scaleControl: true,
				center: startLatLng,
				mapTypeId: mapTypeId
			};
			map = new google.maps.Map(mapContainer.get(0), mapOptions);

			// Only add marker if a location is set
			if (savedLat.val() && savedLng.val()) {
				addMarker();
			} else {
				// No marker should be set, user has prev clicked "remove" or this is first time the map is added
				// So hide position info and add "add marker" link
				metaboxDiv.find(".simple-fields-fieldtype-googlemap-selected-positions").hide();
				metaboxDiv.find(".simple-fields-fieldtype-googlemap-marker-add").show();
			}

			add_listeners();
			infowindow = new google.maps.InfoWindow();
						
		},
		
		add_listeners = function() {

			// Don't submit on enter from this input field. user must select from auto complete.
			metaboxDiv.on("keypress", "input", function(e) {
				if (e.charCode == 13) {
					e.preventDefault();
				}
			});
			
			// Add marker
			metaboxDiv.on("click", ".simple-fields-fieldtype-googlemap-marker-add", function(e) {
				e.preventDefault();
				metaboxDiv.find(".simple-fields-fieldtype-googlemap-marker-add").hide();
				addMarker();
			});
			
			// Remove marker + location info in input fields when click on link
			metaboxDiv.on("click", ".simple-fields-fieldtype-googlemap-marker-remove", function(e) {
				e.preventDefault();

				// Remove marker and close info window
				infowindow.close();
				marker.setVisible(false);

				// hide element with place info, with a smooth transition so the user can get a feeling of what is going on
				metaboxDiv.find(".simple-fields-fieldtype-googlemap-selected-positions").fadeOut("fast", function() {
					// Remove text från text fields and hidden inputs
					metaboxDiv.find("input.simple-fields-field-googlemap-lat").val("");
					metaboxDiv.find("input.simple-fields-field-googlemap-lng").val("");
					metaboxDiv.find(".simple-fields-field-googlemap-selected-lat").text("");
					metaboxDiv.find(".simple-fields-field-googlemap-selected-lng").text("");
					metaboxDiv.find("input.simple-fields-field-googlemap-name").val("");
					metaboxDiv.find("input.simple-fields-field-googlemap-formatted_address").val("");
					metaboxDiv.find("input.simple-fields-field-googlemap-address_components").val("");
					metaboxDiv.find("span.simple-fields-field-googlemap-selected-formatted_address").text("");
					metaboxDiv.find("span.simple-fields-field-googlemap-selected-name").text("");
					metaboxDiv.find(".simple-fields-fieldtype-googlemap-marker-add").show();
				});
			});

			// Add Google Places Autocomplete to the address search
			google.maps.event.addListenerOnce(map, 'idle', function(){

				var address_input = metaboxDiv.find("p.simple-fields-fieldtype-googlemap-address-search input[type='text']");
				var autocomplete_options = {
					bounds: map.getBounds()
				};
				autocomplete = new google.maps.places.Autocomplete(address_input.get(0), autocomplete_options);
				autocomplete.bindTo('bounds', map);
				
				// When place is selected in autocomplete
				google.maps.event.addListener(autocomplete, 'place_changed', function() {
					
					var place = autocomplete.getPlace();
					
					if (place.geometry === undefined) {
						// console.log("No full place selected in autocomplete.");

						// no place found/selected in autocomplete drop down, but perhaps there is a lat lng entered
						// valid lat+lng formats:
						// 48.858278,2.294254
						// 48.858278 2.294254
						// so perhaps minus then number.dot.number pehaps comma then perhaps number then number.dot.number
						var entered_address = address_input.get(0).value;
						var matches = entered_address.match(/(-?\d+\.\d+)[, ](-?\d+\.\d+)/);
						if (matches && matches.length === 3) {
							
							// Woha! Looks like a lat/lng position
							var lat = matches[1];
							var lng = matches[2];
							var latlng = new google.maps.LatLng(lat, lng);

							// Position marker
							// (If no marker is added, add it by click on the add-marker-thingie)
							if (typeof marker === "undefined") {
								metaboxDiv.find(".simple-fields-fieldtype-googlemap-marker-add").trigger("click");
							}

							// Center map to found position + zoom in (because we're pretty sure about the position)
							marker.setPosition(latlng);
							map.setCenter(latlng);
							map.setZoom(17);
							infowindow.open(map, marker);

							// Update/remove string positions if possible aldreay added
							metaboxDiv.find("input.simple-fields-field-googlemap-formatted_address").val("");
							metaboxDiv.find("span.simple-fields-field-googlemap-selected-formatted_address").text("");
							metaboxDiv.find("input.simple-fields-field-googlemap-address_components").val(JSON.stringify([]));
							metaboxDiv.find("input.simple-fields-field-googlemap-name").val("");
							metaboxDiv.find("span.simple-fields-field-googlemap-selected-name").text("");

						}
						return;
					}
					if (place.geometry.viewport) {
						map.fitBounds(place.geometry.viewport);
					} else {
						map.setCenter(place.geometry.location);
						map.setZoom(17);
					}
					
					// If no marker is added, add it by click on the add-marker-thingie
					if (typeof marker === "undefined") {
						metaboxDiv.find(".simple-fields-fieldtype-googlemap-marker-add").trigger("click");
					}
					
					marker.setPosition(place.geometry.location);

					// add infowindow so we better know if we got the right place
					infowindow.setContent(place.name + "<br>" + place.formatted_address);
					// console.log(place.address_components);
					// console.log(place.formatted_address);
					// console.log( JSON.stringify(place.address_components) );
					infowindow.open(map, marker);
					
					// Store and oputput some extra information available for this place
					metaboxDiv.find("input.simple-fields-field-googlemap-formatted_address").val(place.formatted_address);
					metaboxDiv.find("span.simple-fields-field-googlemap-selected-formatted_address").text(place.formatted_address);
					metaboxDiv.find("input.simple-fields-field-googlemap-address_components").val(JSON.stringify(place.address_components));
					metaboxDiv.find("input.simple-fields-field-googlemap-name").val(place.name);
					metaboxDiv.find("span.simple-fields-field-googlemap-selected-name").text(place.name);

				});

			});

		} // add_listeners

	;

	return {

		init : init

	};
	
});

jQuery(function() {
	simple_fields_googlemap.init();
});

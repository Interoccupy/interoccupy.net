=== Simple Fields Map extension ===
Contributors: eskapism, Earth People
Donate link: http://earthpeople.se/
Tags: simple fields, google maps, geolocation, latitude, longitude, lat, lng, location, extension, geocoding
Requires at least: 3.4
Tested up to: 3.4.2
Stable tag: 1.2.2
License: GPLv2

Extension to Simple Fields that adds a field type for selecting a location on a Google Map.

== Description ==

Adds a new field type to [Simple Fields](http://wordpress.org/extend/plugins/simple-fields/) that let you choose a location. 

The coordinates (lat/lng) of that location is saved
and easily retrieved in for example your theme.

Happy geocoding!

#### Features

* Easily add maps to any post, page or custom post type
* Integrates seamlessly into Simple Fields
* You can have multiple maps with separately settings
* Each map can have it's own:
	* zoom level
	* map type (Roadmap, Satellite, Hybrid, Terrain)
	* default location
* Search location of address by using built in search box
* Search location by enter its latitude and longitude coordinates
* Supports Repeatable Fields
* From each saved position you can get
	* Latitude and Longitude
	* Address information, including store/shop name if that was what the user searched for when adding this location

#### Translations/Languages

This plugin is available in the following languages:

* English

== Usage ==

1. (Make sure you have Simple Fields installed)
1. Install the Simple Fields Maps Extension plugin
1. You will find the Map field in the usual settings page of Simple Fields

== Screenshots ==

1. The field with its options
2. The post edit screen, including the currently selected coordinates and a search result found
3. Example of the values that are being stored for each saved location/field (and the function used to get them)

== Changelog ==

= 1.2.2 =
- Fixed notice error if no additional image sizes where used in the theme currently in use

= 1.2.1 =
- Added support for coordinates with negative latitude/longitude locations, like this one: 2.033630,-76.007050
- Added version number to scripts/styles to cache bust.

= 1.2 =
- Added support for adding marker by entering its lng/lat coordinates. Valid formats are: 48.858278,2.294254 or 48.858278 2.294254
- Made plugin translatable

= 1.1.6 =
- Fixed a bug with the latest version of Simple Fields

= 1.1.5 =
- It's tricky to create to class because Simple Fields must be added/loaded already when creating the class. The modified solution works, but perhaps only on specific versions of PHP. Have to check this in more details...

= 1.1.4 =
- A marker/location can now be removed/unset. 
This is useful if you have a map field added to all your posts, but wan't to exclude the map for a single post. 
This is also the default behavior, so a post don't not risk being saved with the wrong position (or at least the risk is lower).

= 1.1.3 =
- Autocomplete was out of control when using repeatable fields. It's now back in control.

= 1.1.2 =
- The search box is now much better. It can search for Places, i.e.  establishments, geographic locations, or prominent points of interest.
- Return values now return much more info if the location was selected through the new autocomplete place search. The return array now also have:
	- The name of the selected place
	- The formatted address of the place
	- A address_components-object thas has (according to Google): a "collection of address components for this Place's location"

= 1.1.1 =
- Modified the way args are passed with return_values, since wp_parse_args don't work recursive.

= 1.1.0 =
- Added static maps to return values.

= 1.0.2 =
- Clearified the fact that you need the latest version of Simple Fields.

= 1.0.1 =
- Typos in readme

= 1.0 =
- Version 1. It works and it's groovy!

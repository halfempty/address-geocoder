=== Address Geocoder ===
Contributors: martyspellerberg, mgibbs189
Tags: geocode, location, latitude, longitude, coordinates, google maps, maps
Requires at least: 4.0
Tested up to: 4.4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add location coordinates to your post types.

== Description ==

The Address Geocoder plugin lets you attach coordinates to your posts, pages, and custom post types. On your edit screens, simply enter an address into the metabox and hit "Geocode".

This plugin uses Google Maps API v3 to translate an address to lat/lng coordinates. A Google Maps API key is required.

== Installation ==

1. Activate the plugin
2. Go to Settings > Address Geocoder to set your Google Maps API key and choose desired post types
3. Done! You'll see a "Geocoder" meta box on your edit screens.

== Fequently Asked Questions ==

= How do I retrieve the coordinates? =

Access your data in the loop using the following functions, with the post ID passed as a parameter. For the full coordinates (in brackets):

`<?php echo get_geocode_latlng( $post->ID ); ?>`

For the Latitude:

`<?php echo get_geocode_lat( $post->ID ); ?>`

For the Longitude:

`<?php echo get_geocode_lng( $post->ID ); ?>`

For the Address:

`<?php echo get_geocode_address( $post->ID ); ?>`

== Screenshots ==

1. The meta box before clicking "Geocode"
2. The meta box after clicking "Geocode"
3. The settings page

== Changelog ==

= 0.9.1 =
* Tested compatibility against WP 4.4

= 0.9 =
* A Google Maps API key is now required. Go to Settings > Address Geocoder to set your Google Maps API key.

= 0.8 =
* Bugfix for settings page

= 0.7 =
* Bugfix for settings page
* Ensured WP 3.9 compatibility

= 0.6 =
* Rewrite using OOP best practices (props @mgibbs189)

= 0.5 =
* Added `get_geocode_lat()` and `get_geocode_lng()` functions

= 0.4 =
* Added settings screen, for disabling metabox by post type

= 0.3 =
* Fixes a bug which caused data to not save

= 0.2 =
* Added support for all post types

= 0.1 =
* First released version.

== Upgrade Notice ==

= 0.9 =

Fixes Google's rejection of the API key. A Google Maps API key is now required.

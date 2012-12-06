=== Address Geocoder ===
Contributors: MartySpellerberg
Tags: geocode, location, maps, plugin
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple WordPress plugin for saving location data with Posts and Pages. Conveniently converts addresses to lat/lng from the Edit screen.

== Description ==

Storing addresses as lat/lng (rather than geocoding each time a user requests a page) decreases load time. This plug-in makes it easy for content managers to generate lat/lng, which is stored with the post as a custom field that can be accessed as part of the loop.

Uses the Google Maps JavaScript API V.3.

== Installation ==

* Download the plugin;
* Unzip and upload it to your /plugins directory;
* Activate the plugin from the Dashboard;
* You should now find a metabox labeled “Geocoder” on your Post and Page Edit screens. Enter an address in the address field (this can be an exact address or the name of a famous place — anything Google will understand);
* Press the “Geocode Address” button;

The Lat/Lng field will populate with a lat/lng object and a map will appear in the preview box, with a marker on the location.

Access your data in the loop using the functions `get_geocode_latlng()` and `get_geocode_address()`, with the post ID passed as a parameter. Such as, for Lat/Lng:

`<?php echo get_geocode_latlng($post->ID); ?>`
 
And for Address:

`<?php echo get_geocode_address($post->ID); ?>`

That’s it!

== Screenshots ==

1. The Address Geocoder metabox on the Post/Page Edit screen

== Changelog ==

= 0.3 =
* Fixes a bug which caused data to not save

= 0.2 =
* Added support for all post types

= 0.1 =
* First released version.
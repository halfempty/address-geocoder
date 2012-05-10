address-geocoder
================

A simple WordPress plugin for saving location data with posts. Conveniently converts addresses to lat/lng from the Post/Page Edit screen. 

This is useful for sites that integrate the Google Maps API, or others. The geocoding is done using the Google Maps JavaScript API V.3. Storing addresses as lat/lng (rather than Geocoding each time a user requests a page) saves load time. This plug-in makes it easy for content managers to generate lat/lng, which is then stored as custom fields that can be accessed as part of the loop.
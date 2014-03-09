(function($) {

    $(function() {

        var map;
        var latlng;
        var markersArray = [];

        setMarker = function(latlng) {
            clearMarkers();
            var marker = new google.maps.Marker({
                map: map,
                position: latlng
            });
            markersArray.push(marker);
        }

        clearMarkers = function() {
            for (var i = 0; i < markersArray.length; i++) {
                markersArray[i].setMap(null);
            }
            markersArray.length = 0;
        }

        // Trigger geocode
        $(document).on('click', '#geocode', function() {
            var address = $('#martygeocoderaddress').val();
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode( { 'address': address }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latlng = results[0].geometry.location;
                    map.setCenter(latlng);
                    setMarker(latlng);

                    $('#martygeocoderlatlng').attr('value', latlng);
                }
                else {
                    alert("Geocode was not successful for the following reason: " + status);
                }
            });
        });

        // Setup the default map
        latlng = $('#martygeocoderlatlng').val();
        latlng = ('' != latlng) ? latlng.substring(1, latlng.length-1).split(', ') : [-34.397, 150.644];
        latlng = new google.maps.LatLng(latlng[0], latlng[1]);

        map = new google.maps.Map(document.getElementById('geocodepreview'), {
            mapTypeControl: false,
            zoom: 11,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        setMarker(latlng);
    });

})(jQuery);
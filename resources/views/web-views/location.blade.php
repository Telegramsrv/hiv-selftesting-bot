<!DOCTYPE html>
<html>
  <head>
    <style>
#map {
height: 1000px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <!--<h3>Map</h3>-->
    <div id="map"></div>
    <script>
      function initMap() {
          var uluru = {lat: {{$lat}}, lng: {{$lng}}};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBgB1xkEOxSD0-SIpVbs3lMf0297fCiiY&callback=initMap">
    </script>
  </body>
</html>
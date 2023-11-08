<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: map.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Map</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/RFID_STICKER_LOGO.jpg" href="">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
</head>

<body>

<?php include'header.php';?>

<main>
<section class="mapboxgl-canvas" tabindex="0" aria-label="Map" role="region" width="1336" height="500" style="width: 1200px; height: 1200px;">
  <head>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>Display a map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
       #map {position: absolute; top: 0; right: 0; bottom: 0; left: 0;}
    </style>
  </head>

  <body>
    <div id="map">
      <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
    </div>
     <p><a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a></p>

    <script>
      const key = 'YOUR_MAPTILER_API_KEY_HERE';
      const map = L.map('map').setView([7.06569722, 125.59678861], 14); //starting position
      L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${key}`,{ //style URL
        tileSize: 512,
        zoomOffset: -1,
        minZoom: 20.5,
        attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
        crossOrigin: true
      }).addTo(map);

      var myGeoJSON = {"type": "FeatureCollection", "features": [{"type": "Feature", "geometry": {"type": "Polygon", "coordinates": [[[125.59640375, 7.06583173], [125.59632507, 7.06562943], [125.59634742, 7.06556644], [125.59637603, 7.06556378], [125.59639928, 7.06551586], [125.59637156, 7.06544222], [125.59684363, 7.06527896], [125.59699652, 7.06568533], [125.59704569, 7.0657084], [125.59718248, 7.0656596], [125.59725222, 7.06585037], [125.59698311, 7.06594619], [125.59699115, 7.06598168], [125.59662011, 7.06611566], [125.59658167, 7.06602072], [125.5966228, 7.06600653], [125.59664604, 7.06593288], [125.59640375, 7.06583173]]]}, "id": "434c991f-7512-42d6-a587-deac996c0250", "properties": {"name": "BE_Building", "BE_Building": ""}}]}
      L.geoJSON(myGeoJSON).addTo(map)

    </script>
  </body>

</section>
</main>
</body>
</html>



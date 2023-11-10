<?php

session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Map</title>

    <link rel="stylesheet" type="text/css" href="css/map.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>

</head>

<body>
    <?php include 'header.php'; ?>

    <main>
    <h1 class="slideInDown animated">Map</h1>

        <section id="map" aria-label="Map" role="region" position="absolute" >
            <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
            <p><a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a></p>
        </section>
    </main>

    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>Vector Tiles in Leaflet JS</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.css" rel="stylesheet" />
    <script src="https://cdn.maptiler.com/leaflet-maptilersdk/v1.0.0/leaflet-maptilersdk.js"></script>

    <script>
        // Declare isAdding outside the script block
        var isAdding = false;

        // Leaflet map initialization
        const key = 'aF7HhncV5bhT2pqqWdRV';
        const map = L.map('map').setView([7.06569722, 125.59678861], 14);

        // Set the maxZoom and minZoom properties
        map.options.maxZoom = 25; // Adjust this value as needed for your requirements
        map.options.minZoom = 17; // Adjust this value as needed


        L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=aF7HhncV5bhT2pqqWdRV`, {
            tileSize: 512,
            zoomOffset: -1,
            attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
            crossOrigin: true
        }).addTo(map);

        // Add GeoJSON data to the map
        var myGeoJSON = {
          "type": "FeatureCollection",
          "features": [
            {
              "type": "Feature",
              "geometry": {
                "type": "Polygon",
                "coordinates": [
                  [
                    [125.59657807, 7.06560118],
                    [125.59664244, 7.06578485],
                    [125.596739, 7.06582212],
                    [125.59689055, 7.06576622],
                    [125.59679533, 7.06551734],
                    [125.59657807, 7.06560118]
                  ]
                ]
              },
              "id": "73ace24a-738a-49a2-a1a0-9dea8251bb20",
              "properties": {
                "name": "",
                "Open space": ""
              }
            },
            {
              "type": "Feature",
              "geometry": {
                "type": "Polygon",
                "coordinates": [
                  [
                    [125.59648312, 7.06572744],
                    [125.59652759, 7.06584043],
                    [125.5964658, 7.06585894],
                    [125.59640002, 7.06583076],
                    [125.59637449, 7.06576338],
                    [125.59648312, 7.06572744]
                  ]
                ]
              },
              "id": "ba1cf84b-5911-4090-86dd-3206038d059b",
              "properties": {
                "name": "BE 213"
              }
            },
            {
              "type": "Feature",
              "geometry": {
                "type": "Polygon",
                "coordinates": [
                  [
                    [125.59684311, 7.0652798],
                    [125.59637025, 7.06544318],
                    [125.59639652, 7.06550748],
                    [125.5963755, 7.0655631],
                    [125.59634398, 7.06557701],
                    [125.59632471, 7.06562741],
                    [125.59640002, 7.06583076],
                    [125.59664346, 7.06593504],
                    [125.59661544, 7.06600804],
                    [125.59658216, 7.06602021],
                    [125.59662419, 7.0661158],
                    [125.59699373, 7.06598023],
                    [125.59698322, 7.06594895],
                    [125.59725818, 7.06585162],
                    [125.59718112, 7.06565869],
                    [125.59704627, 7.06570562],
                    [125.59699373, 7.06568824],
                    [125.59684311, 7.0652798]
                  ]
                ]
              },
              "id": "19f98cab-a9e8-440e-8b7a-3aac7f2f6f68",
              "properties": {
                "name": "BE building",
                "BE 216": "",
                "BE Building": ""
              }
            },
            {
              "type": "Feature",
              "geometry": {
                "type": "Polygon",
                "coordinates": [
                  [
                    [125.59633693, 7.06566041],
                    [125.59644047, 7.06562166],
                    [125.59648312, 7.06572744],
                    [125.59637449, 7.06576338],
                    [125.59633693, 7.06566041]
                  ]
                ]
              },
              "id": "51fe7eb2-7883-4fda-8413-0bc078ce06a2",
              "properties": {
                "name": "BE 216"
              }
            }
          ]
        };


        var geojsonLayer = L.geoJSON(myGeoJSON).addTo(map);

        // Bind a popup to the GeoJSON layer
       // geojsonLayer.bindPopup("<b>BE Building</b>");


        var bounds = [Infinity, Infinity, -Infinity, -Infinity];

    // Create a layer group to store the markers
    var markerLayer = L.layerGroup().addTo(map);

    // The CSS to style the custom marker
    var customMarkerStyle = `
      .custom-icon {
        width: 32px;
        height: 32px;
        margin-left: -16px;
        margin-top: -32px;
        text-align: center;
      }
      .marker-icon {
        width: 16px;
        height: 16px;
        border: 2px solid white;
        border-radius: 50%;
        cursor: grab;
      }
    `;

    // Create a custom CSS style for the marker
    var customIconStyle = L.divIcon({
        className: 'custom-icon',
        html: '<div class="marker-icon" style="background-color: blue;"></div>',
        draggable: true // Enable dragging
    });

    // Include the custom marker style in the map's CSS
    var customMarkerStyleElement = document.createElement('style');
    customMarkerStyleElement.type = 'text/css';
    customMarkerStyleElement.appendChild(document.createTextNode(customMarkerStyle));
    document.head.appendChild(customMarkerStyleElement);

    // Function to create a popup with latitude and longitude
    function createPopup(latlng) {
        const lat = latlng.lat;
        const lng = latlng.lng;

        // Create a popup and set its content
        const popupContent = "Latitude: " + lat + "<br>Longitude: " + lng;
        var popup = L.popup()
            .setLatLng(latlng)
            .setContent(popupContent);

        // Open the popup on the map
        popup.openOn(map);
    }



     map.on('click', function (e) {
            if (isAdding) {
                var marker = L.marker(e.latlng, { icon: customIconStyle });
                markerLayer.addLayer(marker);
                marker.dragging.enable();
                marker.on('dragend', function (event) {
                    var marker = event.target;
                    var position = marker.getLatLng();
                    console.log('Marker was dragged to: Lat: ' + position.lat + ', Long: ' + position.lng);
                    createPopup(position);
                    updateMarkersInStorage();
                });
                marker.on('click', function (event) {
                    var marker = event.target;
                    var position = marker.getLatLng();
                    createPopup(position);
                });
                updateMarkersInStorage();
            }
        });


    // Add a scale control
    L.control.scale({
        metric: true,
        imperial: false,
        position: 'topright'
    }).addTo(map);


    // Button to toggle adding markers
    const addButton = document.createElement('button');
        addButton.textContent = 'Add Blue Icons';
        addButton.id = 'addButton';
        addButton.addEventListener('click', function () {
            isAdding = !isAdding;
            addButton.textContent = isAdding ? 'Stop Adding' : 'Add Blue Icons';
        });

    addButton.style.position = 'absolute';
    addButton.style.top = '300px';
    addButton.style.right = '200px';

    // Button to save markers
    const saveButton = document.createElement('button');
    saveButton.textContent = 'Save Markers';
    saveButton.style.position = 'absolute';
    saveButton.style.top = '350px';
    saveButton.style.right = '208px';
    saveButton.addEventListener('click', function () {
        updateMarkersInStorage();
        alert('Markers saved!');
    });

    const resetButton = document.createElement('button');
    resetButton.textContent = 'Reset Markers';
    resetButton.style.position = 'absolute';
    resetButton.style.top = '400px';
    resetButton.style.right = '204px';
    resetButton.addEventListener('click', function () {
        resetMarkers();
    });

    const renameButton = document.createElement('button');
    renameButton.textContent = 'Rename Marker';
    renameButton.style.position = 'absolute';
    renameButton.style.top = '450px';
    renameButton.style.right = '200px';

    let selectedMarker = null;

    renameButton.addEventListener('click', function () {
        if (selectedMarker) {
            var newName = prompt('Enter the new name for this location:', selectedMarker.options.title);
            if (newName !== null) {
                selectedMarker.options.title = newName;
                updateMarkersInStorage();
            }
        } else {
            alert('Select a marker to rename.');
        }
    });


    function resetMarkers() {
    // Ask for confirmation
    var confirmReset = confirm('Are you sure you want to reset the markers? This action cannot be undone.');

    if (confirmReset) {
        // Remove all markers from the marker layer
        markerLayer.clearLayers();

        // Clear markers from local storage
        localStorage.removeItem('markers');
    }
    }

     // Load markers from local storage if available
        var storedMarkers = localStorage.getItem('markers');
        if (storedMarkers) {
            var confirmReload = confirm('Do you want to load the saved markers?');
            if (confirmReload) {
                var parsedMarkers = JSON.parse(storedMarkers);
                parsedMarkers.forEach(function (markerData) {
                    var latlng = L.latLng(markerData.lat, markerData.lng);
                    var marker = L.marker(latlng, { icon: customIconStyle });
                    markerLayer.addLayer(marker);
                    marker.dragging.enable();
                    marker.on('dragend', function (event) {
                        var marker = event.target;
                        var position = marker.getLatLng();
                        updateMarkersInStorage();
                    });
                    updateMarkersInStorage();
                });
            } else {
                localStorage.removeItem('markers');
            }
        }

        function updateMarkersInStorage() {
            var markers = markerLayer.getLayers().map(function (marker) {
                return {
                    lat: marker.getLatLng().lat,
                    lng: marker.getLatLng().lng
                };
            });
            localStorage.setItem('markers', JSON.stringify(markers));
        }

        window.onbeforeunload = function (event) {
            if (markerLayer.getLayers().length > 0) {
                return 'You have unsaved markers. Do you really want to leave?';
            }
        };

    // Add the button to the page body
    document.body.appendChild(addButton);
    document.body.appendChild(saveButton);
    document.body.appendChild(resetButton);
    document.body.appendChild(renameButton);
</script>

</body>
</html>

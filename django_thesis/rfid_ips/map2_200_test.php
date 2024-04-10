<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';

session_start();

if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}

if (isset($_POST['saveButton']) && isset($_POST['markers2_200'])) {
    // Save markers2_200 to the database
    $markers = json_decode($_POST['markers2_200'], true);

    foreach ($markers as $marker) {
        $lat = $marker['lat'];
        $lng = $marker['lng'];
        $title = $marker['title']; // Assuming 'name' is a unique identifier

        // Check if the marker already exists in the database
        $query = "SELECT * FROM markers2_200 WHERE title = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $title);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            echo "Error executing query: " . mysqli_error($conn);
        }

        // If the marker exists, update its position
        if (mysqli_num_rows($result) > 0) {
            $updateQuery = "UPDATE markers2_200 SET lat = ?, lng = ? WHERE title = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'dds', $lat, $lng, $title);
            mysqli_stmt_execute($updateStmt);

            if (!$updateStmt) {
                echo "Error updating marker: " . mysqli_error($conn);
            }
        } else {
            // If the marker doesn't exist, insert a new record
            $insertQuery = "INSERT INTO markers2_200 (title, lat, lng) VALUES (?, ?, ?)";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, 'sdd', $title, $lat, $lng);
            mysqli_stmt_execute($insertStmt);

            if (!$insertStmt) {
                echo "Error inserting marker: " . mysqli_error($conn);
            }
        }
    }

    echo "markers2_200 saved successfully!";
} elseif (isset($_POST['loadButton'])) {
    // Load markers2_200 from the database
    $markers = array();

    $query = "SELECT * FROM markers2_200";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $markers[] = array(
                'title' => $row['title'],
                'lat' => $row['lat'],
                'lng' => $row['lng']
            );
        }

        // Return the data as JSON
        echo json_encode($markers2_200);
    } else {
        echo "Error retrieving data from the database";
    }
} else {
    echo "Invalid request";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Map</title>

    <link rel="stylesheet" type="text/css" href="css/map_test.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>

</head>

<body>
    <?php include 'header.php'; ?>

    <main>
    <h1 class="slideInDown animated">Map</h1>

        <section id="map" aria-label="Map" role="region" position="absolute" >
            <a href="https://www.maptiler.com" style="position:absolute; z-index:500;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
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

        // Declare markers2_200 as a global variable
        var markers2_200 = [];

        console.log("markers2_200:", markers2_200);

        // Leaflet map initialization
        const key = 'aF7HhncV5bhT2pqqWdRV';
        const map = L.map('map', {
            preferCanvas: true
        }).setView([7.06569722, 125.59678861], 14);

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

    // Add a scale control
    L.control.scale({
        metric: true,
        imperial: false,
        position: 'topright'
    }).addTo(map);

// CODE FOR THE POPUP LATITUDE, LONGITUDE, AND BLUE ICON NAMES /////////////////////////////////////////////////////

    // Function to create a popup with latitude, longitude, and delete option
function createPopup(latlng, title) {
    if (!latlng || typeof latlng.lat === 'undefined' || typeof latlng.lng === 'undefined') {
        console.error('Invalid latlng object:', latlng);
        return;
    }

    const lat = latlng.lat;
    const lng = latlng.lng;

    // Create a popup
    var popup = L.popup()
        .setLatLng(latlng);

    // Initialize popupContent
    let popupContent = "Latitude: " + lat + "<br>Longitude: " + lng + "<br>Name: " + title + "<br><button onclick='deleteMarker(\"" + title + "\", " + latlng.lat + "," + latlng.lng + ")'";

    // Check if title is 'undefined', update popupContent accordingly
    if (typeof title === 'undefined') {
        popupContent = "Latitude: " + lat + "<br>Longitude: " + lng;
    }

    // Set the content of the popup
    popup.setContent(popupContent);

    // Add the marker reference to the popup for access in the delete function
    popup.marker = title;

    // Open the popup on the map
    popup.openOn(map);

    // Add the delete button inside the popup
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Delete Marker';
    deleteButton.addEventListener('click', function () {
        const markerToDelete = findMarkerByName(title);

        if (markerToDelete) {
            deleteMarker(markerToDelete);
        } else {
            // Provide a user-friendly message or handle the situation accordingly
            alert('Marker with name ' + title + ' not found.');
            console.warn('Marker not found:', title);
        }
    });

    // Append the delete button to the popup
    popup._contentNode.appendChild(deleteButton);
}




// DELETING OF THE BLUE ICON //////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Function to delete a marker
    function deleteMarker(marker) {
        if (marker) {
            const title = marker.options.title;
            const lat = marker.getLatLng().lat;
            const lng = marker.getLatLng().lng;

            // Remove the marker from the marker layer
            markerLayer.removeLayer(marker);

            // Update the marker in local storage
            updateMarkersInStorage();

            // Update the marker in the database
            deleteMarkerInDatabase(title, lat, lng);
        } else {
            console.error('Invalid marker object:', marker);
        }
    }

// RESET BUTTON FOR THE BLUE ICONS /////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Function to reset the blue icons
function resetMarkers() {
    // Ask for confirmation
    var confirmReset = confirm('Are you sure you want to reset the blue icons? This action cannot be undone.');

    if (confirmReset) {
        // Remove all markers2_200 from the marker layer
        markerLayer.clearLayers();

        // Clear markers2_200 from local storage
        localStorage.removeItem('markers2_200');

        // Reset markers2_200Saved when markers2_200 are reset
        markersSaved = false;

        // Reset the button text
        saveButton.textContent = 'Save markers2_200';

        // Reset the delete mode
        deleteMode = false;
        deleteButton.textContent = 'Delete Blue Icons';

        // Turn off the delete mode event listener
        map.off('click', handleDeleteClick);
    }
}

    const resetButton = document.createElement('button');
    resetButton.textContent = 'Reset markers2_200';
    resetButton.classList.add('reset-button'); // Apply the CSS class
    resetButton.addEventListener('click', function () {
        resetMarkers();
    });

        function resetMarkers() {
            // Check if markers2_200 are saved, and reset markers2_200Saved without prompting if they are
            if (markersSaved) {
                markersSaved = false; // Reset markers2_200Saved when markers2_200 are reset
                saveButton.textContent = 'Save markers2_200'; // Reset the button text
            } else {
                var confirmReset = confirm('Are you sure you want to reset the markers2_200? This action cannot be undone.');

                if (!confirmReset) {
                    return;
                }
            }

            // Remove all markers2_200 from the marker layer
            markerLayer.clearLayers();

            // Clear markers2_200 from local storage
            localStorage.removeItem('markers2_200');
        }

    resetButton.addEventListener('click', function () {
            resetMarkers();
    });

// ADD BLUE ICONS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Button to toggle adding markers
    const addButton = document.createElement('button');
    addButton.textContent = 'Add Blue Icons';
    addButton.id = 'addButton';
    addButton.classList.add('add-button'); // Apply the CSS class
    addButton.addEventListener('click', function () {
        isAdding = !isAdding;
        addButton.textContent = isAdding ? 'Stop Adding' : 'Add Blue Icons';
    });

    // Modify the click event listener to handle renaming
    map.on('click', function (e) {
        if (isAdding) {
            // Prompt the user for the name of the location
            var locationName = prompt('Enter the name for this location:');

            // Check if the user clicked "Cancel" or entered an empty name
            if (locationName !== null || locationName.trim() !== '') {
                // Call the addMarker function to add the marker
                //addMarker(e.latlng.lat, e.latlng.lng, locationName);
            }

            // Create a blue icon marker at the clicked location with the specified name
            var marker = L.marker(e.latlng, {
                icon: customIconStyle,
                title: locationName
            });

            // Set the name as the marker's title
            marker.options.title = locationName;
            console.log("locationName: ", locationName);

            // Add the marker to the marker layer
            markerLayer.addLayer(marker);

            // Make the marker draggable
            marker.dragging.enable();

            // Declare position variable outside the dragend event handler
            var position;

            // Handle dragend event to update marker position and open popup
            marker.on('dragend', function (event) {
                marker = event.target;
                position = marker.getLatLng();
                console.log('Marker was dragged to: Lat: ' + position.lat + ', Long: ' + position.lng);

                // Update the popup position
                createPopup(position, marker.options.title); // Pass the title to createPopup
                updateMarkerInDatabase(marker.options.title, position.lat, position.lng); // Update marker in the database
                updateMarkersInStorage();
            });

            // Open popup on marker click
            marker.on('click', function (event) {
                // Use the position variable declared in the outer scope
                createPopup(position, marker.options.title);
            });

            // Update the popup position
            createPopup(e.latlng, locationName);
            updateMarkerInDatabase(marker.options.title, e.latlng.lat, e.latlng.lng); // Update marker in the database
        }
    });




    markerLayer.eachLayer(function (marker) {
    var latlng = marker.getLatLng();
    var title = marker.options.title; // Assuming you set the title as the name
    markers2_200.push({ title: title, lat: latlng.lat, lng: latlng.lng });
    });

    // Helper function to find a marker by its coordinates
    function findMarkerByLatLng(latlng) {
        var markers2_200 = markerLayer.getLayers();
        for (var i = 0; i < markers2_200.length; i++) {
            if (markers2_200[i].getLatLng().equals(latlng)) {
                return markers2_200[i];
            }
        }
        return null;
    }

    // Function to find a marker by its title
    function findMarkerByName(title) {
        var markers2_200 = markerLayer.getLayers();
        for (var i = 0; i < markers2_200.length; i++) {
            if (markers2_200[i].options.title === title) {
                console.log("markers2_200:", markers2_200[i]);
                return markers2_200[i];
            }
        }
        return null;
    }

// SAVED BUTTON //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   // Updated variable to track whether markers2_200 are saved
        var markersSaved = false;

        // Button to save markers2_200
        const saveButton = document.createElement('button');
        saveButton.textContent = 'Save markers2_200';
        saveButton.classList.add('save-button'); // Apply the CSS class
        saveButton.addEventListener('click', function () {
            updateMarkersInStorage();
            markersSaved = true;
            saveButton.textContent = 'Saved';

            setTimeout(function () {
                saveButton.textContent = 'Save Markers';
                markersSaved = false;
            }, 3000);
        });

   // Update the existing event listener
   saveButton.addEventListener('click', function () {
            saveMarkers();
            updateMarkersInStorage();
            markersSaved = true;
            saveButton.textContent = 'Saved';
            setTimeout(function () {
                saveButton.textContent = 'Save Markers';
                markersSaved = false;
            }, 3000);
   });

   // Function to save markers2_200
   function saveMarkers() {
        var markers2_200 = [];
        markerLayer.eachLayer(function (marker) {
            var latlng = marker.getLatLng();
            var title = marker.options.title; // Assuming you set the title as the name
            markers2_200.push({ title: title, lat: latlng.lat, lng: latlng.lng });
        });

        console.log("AJAX URL:", "map2_200_test.php");
        // AJAX request to save markers to the database
        $.ajax({
            type: "POST",
            url: "map2_200_test.php", // Replace with the actual filename
            data: { saveButton: true, markers2_200: JSON.stringify(markers2_200) },
            success: function (response) {
                alert(response); // Display the server response
                // Optionally, update the button text or perform other actions
            },
            error: function (xhr, status, error) {
                console.error("Error saving markers:", error);
                console.log(xhr.responseText); // Log the full response for debugging
            }
        });
   }

// LOAD MARKER /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



// Declare addMarker function
function addMarker(lat, lng, title) {
    // Create a blue icon marker at the specified location with the specified name
    var marker = L.marker([lat, lng], {
        icon: customIconStyle,
        title: title
    }).addTo(map);

    // Make the marker draggable
    marker.dragging.enable();

    // Declare position variable outside the dragend event handler
    var position;

    // Handle dragend event to update marker position and open popup
    marker.on('dragend', function (event) {
        marker = event.target;
        position = marker.getLatLng();
        console.log('Marker was dragged to: Lat: ' + position.lat + ', Long: ' + position.lng);

        // Update the popup position
        createPopup(position, title); // Pass the title to createPopup
        updateMarkerInDatabase(title, position.lat, position.lng); // Update marker in the database
        updateMarkersInStorage();
    });

    // Open popup on marker click
    marker.on('click', function (event) {
        // Use the position variable declared in the outer scope
        createPopup(position, title);
    });

    // Update the popup position
    createPopup({ lat: lat, lng: lng }, title);
    updateMarkerInDatabase(title, lat, lng); // Update marker in the database
    updateMarkersInStorage();
}


// DATABASE ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   // Function to update the marker in the database
   function updateMarkerInDatabase(title, lat, lng) {
        console.log("Updating marker:", title, lat, lng);

        // AJAX request to update the marker in the database
        $.ajax({
            type: "POST",
            url: "map2_200_update_marker.php", // Replace with the actual filename or endpoint
            data: { title: title, lat: lat, lng: lng },
            success: function (response) {
                console.log("Marker updated in the database:", response);
            },
            error: function (xhr, status, error) {
                console.error("Error updating marker in the database:", error);
                console.log(xhr.responseText); // Log the full response for debugging
            }
        });
   }

    // Function to delete a marker from the database
    function deleteMarkerInDatabase(title, lat, lng) {
        // AJAX request to delete the marker from the database
        $.ajax({
            type: "POST",
            url: "map2_200_delete_marker.php", // Replace with the actual filename or endpoint
            data: { title: title, lat: lat, lng: lng }, // Match keys with PHP script
            success: function (response) {
                console.log("Marker deleted from the database:", response);
            },
            error: function (xhr, status, error) {
                console.error("Error deleting marker from the database:", error);
                console.log(xhr.responseText); // Log the full response for debugging
            }
        });
    }


   // Load markers2_200 from local storage if available, only if markers2_200 are not saved
   var storedMarkers = localStorage.getItem('markers2_200');
   if (storedMarkers && !markersSaved) {
        var confirmReload = confirm('Do you want to load the saved markers?');
        if (confirmReload) {
            var parsedMarkers = JSON.parse(storedMarkers);
            parsedMarkers.forEach(function (markerData) {
                var latlng = L.latLng(markerData.lat, markerData.lng);
                var marker = L.marker(latlng, {
                    icon: customIconStyle,
                    title: markerData.title // Access the GeoJSON property for the marker name
                });
                markerLayer.addLayer(marker);
                marker.dragging.enable();
                marker.on('dragend', function (event) {
                    var marker = event.target;
                    var position = marker.getLatLng();
                    updateMarkerInDatabase(marker.options.title, position.lat, position.lng); // Update marker in the database
                    updateMarkersInStorage();
                });
            });
            updateMarkersInStorage();
        } else {
            localStorage.removeItem('markers2_200');
        }
   }

   function updateMarkersInStorage() {

        var markers2_200 = markerLayer.getLayers().map(function (marker) {
                return {
                    lat: marker.getLatLng().lat,
                    lng: marker.getLatLng().lng
                };
            });
            localStorage.setItem('markers2_200', JSON.stringify(markers2_200));
        }

        window.onbeforeunload = function (event) {
            if (markerLayer.getLayers().length > 0) {
                return 'You have unsaved markers. Do you really want to leave?';
            }
   };

   // Load markers2_200 from the database
    function loadMarkersFromDatabase() {
        // AJAX request to load markers from the database
        $.ajax({
            type: "POST",
            url: "map2_200_load_marker.php",
            data: { loadButton: true },
            success: function (response) {
                console.log("Response from the server:", response);

                try {
                    // Parse the JSON response
                    var databaseMarkers = JSON.parse(response.trim());

                    // Log the structure of the first marker's data
                    if (databaseMarkers.length > 0) {
                        console.log("First marker's data:", databaseMarkers[0]);
                    } else {
                        console.log("No markers returned from the database.");
                    }

                    // Add markers to the map
                    databaseMarkers.forEach(function (markerData) {
                        addMarker(markerData.lat, markerData.lng, markerData.title);
                    });
                } catch (parseError) {
                    console.error("Error parsing JSON:", parseError);
                    console.log("Invalid JSON string:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading markers from the database:", error);
                console.log(xhr.responseText); // Log the full response for debugging
            }
        });
    }



// Call the function to load markers from the database
loadMarkersFromDatabase();

// BUTTONS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Add the button to the page body
document.body.appendChild(addButton);
document.body.appendChild(saveButton);
document.body.appendChild(resetButton);


console.log("storedMarkers:", storedMarkers);
console.log("markersSaved:", markersSaved);

</script>

</body>
</html>




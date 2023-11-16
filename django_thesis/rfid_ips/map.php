<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';

// Check if the macAddress parameter is set in the request
$Macaddress = isset($_GET['Macaddress']) ? $_GET['Macaddress'] : null;

if ($Macaddress !== null) {
    // Query the database to retrieve the user's username
    $query = "SELECT username FROM users WHERE Macaddress = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $Macaddress);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    // Return the username as JSON
    header('Content-Type: application/json');
    echo json_encode(['userName' => $username]);
}
else {
    // Handle the case where macAddress is not provided
    header('Content-Type: application/json');
    echo json_encode(['error' => 'MAC address not provided']);
}

// Array to store CSV file paths
$csvFiles = [
    'C:/Users/Thesis2.0/django_thesis/restAPI/scanned_aps_cap1.csv',
    'C:/Users/Thesis2.0/django_thesis/restAPI/scanned_aps_cap2.csv',
    'C:/Users/Thesis2.0/django_thesis/restAPI/scanned_aps_cap3.csv',
];

// Function to read CSV file and return data as an array
function readCSV($csvFile)
{
    $csvData = [];
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $csvData[] = $data;
        }
        fclose($handle);
    }
    return $csvData;
}

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
    <div class="form-style-5 slideInDown animated">
        <form enctype="multipart/form-data">
            <div class="alert_user"></div>
            <fieldset>
                <legend><span class="number">1</span> Online User</legend>
                <?php
                foreach ($csvFiles as $csvFile) {
                    $csvData = readCSV($csvFile);
                    echo '<h3>Data from ' . basename($csvFile) . '</h3>';
                    echo '<table class="tbl-content">';
                    echo '<thead>';
                    echo '<tr><th>MAC</th><th>SSID</th><th>Signal Strength</th><th>Source</th><th>User Name</th></tr>';
                    echo '</thead>';
                    echo '<tbody>'; 
                    foreach ($csvData as $row) {
                        echo '<tr>';
                        foreach ($row as $cell) {
                            echo '<td>' . htmlspecialchars($cell) . '</td>';
                        }

                        // Fetch and display user name based on MAC address
                        $Macaddress = $row[0]; // Assuming MAC address is in the first column

                        // Query the database to retrieve the user's username
                        $query = "SELECT username FROM users WHERE Macaddress = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $Macaddress);
                        $stmt->execute();
                        $stmt->bind_result($username);
                        $stmt->fetch();
                        $stmt->close();

                        if ($username) {
                            echo '<td>' . $username . '</td>';
                        } else {
                            echo '<td>No User Found</td>';
                        }

                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                }
                ?>
            </fieldset>
        </form>
    </div>
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
        // Declare markers as a global variable
        var markers = [];

        console.log("Markers:", markers);

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

        L.control.scale({
            metric: true,
            imperial: false,
            position: 'topright'
        }).addTo(map);

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


        // OLD CODE
        /*
        if(!navigator.geolocation) {
        console.log("Your browser doesn't support geolocation feature!")
        } else {
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(getPosition)
            }, 5000);
        }

    var marker, circle;

    function getPosition(position){
        // console.log(position)
        var lat = position.coords.latitude
        var long = position.coords.longitude
        var accuracy = position.coords.accuracy

        if(marker) {
            map.removeLayer(marker)
        }

        if(circle) {
            map.removeLayer(circle)
        }

        marker = L.marker([lat, long])
        circle = L.circle([lat, long], {radius: accuracy})

        var featureGroup = L.featureGroup([marker, circle]).addTo(map)

        //map.fitBounds(featureGroup.getBounds())

        console.log("Your coordinate is: Lat: "+ lat +" Long: "+ long+ " Accuracy: "+ accuracy)
        }*/

        // Load the final_predicted_values_aggregated.csv file with a timestamp to prevent caching
        const csvFilePath = 'css/final_predicted_values_aggregated.csv?' + Date.now();

        // Inject the custom marker CSS into the document
        const style = document.createElement('style');
        style.innerHTML = customMarkerStyle;
        document.head.appendChild(style);

        // Create a custom CSS style for the marker
        const customIconStyle = L.divIcon({
          className: 'custom-icon',
          html: '<div class="marker-icon" style="background-color: blue;"></div>',
          draggable: true // Enable dragging
        });

        // Function to add markers to the map
        async function addMarkers(data) {
          // Clear existing markers
          map.eachLayer(layer => {
            if (layer instanceof L.Marker) {
              map.removeLayer(layer);
            }
          });

          for (const row of data) {
            const lat = parseFloat(row.lat);
            const lng = parseFloat(row.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
              // Fetch user name based on MAC address from the database
              const userName = await fetchUserNameFromDatabase(row.mac_address);

              // Create marker with user name in the popup
              L.marker([lat, lng], { icon: customIconStyle }).addTo(map)
                .bindPopup(`<b>${userName}</b><br>MAC Address: ${row.mac_address}<br>Latitude: ${lat}<br>Longitude: ${lng}`);
            }
          }
        }

        // Function to fetch user name from the database based on MAC address
        async function fetchUserNameFromDatabase(Macaddress) {
          try {
            const response = await fetch(`map_fetchUsername.php?Macaddress=${encodeURIComponent(Macaddress)}`);
            const data = await response.json();

            if (data.error) {
              console.error('Error fetching user name:', data.error);
              return 'Unknown User';
            }

            // Assume that the response contains the user name
            return data.userName;
          } catch (error) {
            console.error('Error fetching user name:', error);
            return 'Unknown User';
          }
        }




        // Function to fetch and update CSV data
        async function updateCSV() {
          console.log('Updating CSV...');
          try {
            const response = await fetch(csvFilePath);
            if (response.ok) {
              const data = await response.text();

              // Parse CSV data and add markers
              const csvData = Papa.parse(data, { header: true });
              addMarkers(csvData.data);
            } else {
              console.error('Failed to fetch CSV:', response.status, response.statusText);
            }
          } catch (error) {
            console.error('Error fetching CSV:', error);
          }
        }

        // Initial call to load markers
        updateCSV();

        // Set interval to update markers every 1 second
        setInterval(updateCSV, 1000);

        // Include PapaParse library for CSV parsing
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js';
        script.onload = updateCSV; // Call updateCSV when PapaParse is loaded
        document.head.appendChild(script);


    map.on('click', function (e) {
    const latlng = e.latlng;
    const lat = latlng.lat;
    const lng = latlng.lng;

    // Create a popup and set its content
    const popupContent = "Latitude: " + lat + "<br>Longitude: " + lng;
    const popup = L.popup()
        .setLatLng(latlng)
        .setContent(popupContent);

    // Open the popup on the map
    popup.openOn(map);
    });

    </script>
</body>
</html>

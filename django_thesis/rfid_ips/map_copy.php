<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';



// Array to store CSV file paths
$csvFiles = [
    'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/final_predicted_values_aggregated.csv'
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
                // Create an associative array to store the merged data
                $mergedData = [];
                $roomData = []; // Array to store room information

                foreach ($csvFiles as $csvFile) {
                    $csvData = readCSV($csvFile);

                    foreach ($csvData as $row) {
                        // Fetch and display user details based on MAC address
                        $ssid = $row[0]; // Assuming MAC address is in the first column

                        // Query the database to retrieve the user's details
                        $query = "SELECT username, serialnumber, sex, Contact, EmergencyContact, MedicalHistory FROM users WHERE ssid = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $ssid);
                        $stmt->execute();
                        $stmt->bind_result($username, $serialnumber, $sex, $Contact, $EmergencyContact, $MedicalHistory);
                        $stmt->fetch();
                        $stmt->close();

                        // Skip rows with empty or 'root' username
                        if (empty($username) || $username === 'root') {
                            continue;
                        }

                        // If username already exists in the mergedData array, merge the details
                        if (isset($mergedData[$username])) {
                            // Add the details to the array if they are not already present
                            if (!in_array($serialnumber, $mergedData[$username]['serialnumbers'])) {
                                $mergedData[$username]['serialnumbers'][] = $serialnumber;
                            }
                            // Similar for other details...
                        } else {
                            // If username does not exist, create a new entry in the mergedData array
                            $mergedData[$username] = [
                                'serialnumbers' => [$serialnumber],
                                'sex' => $sex,
                                'Contact' => $Contact,
                                'EmergencyContact' => $EmergencyContact,
                                'MedicalHistory' => $MedicalHistory,
                                'timestamp' => $row[2], // Assuming timestamp is in the 6th column (adjust if needed)
                                'username' => $username,
                            ];
                        }

                        // Determine the room based on the 'predicted_floorid'
                        $predictedFloorId = (int)$row[3]; // Assuming predicted_floorid is in the 4th column (adjust if needed)
                        if ($predictedFloorId >= 101 && $predictedFloorId <= 189) {
                            $room = 'BE213';
                        } elseif ($predictedFloorId >= 1 && $predictedFloorId <= 80) {
                            $room = 'BE214';
                        } else {
                            $room = 'Unknown Room';
                        }

                        // Store room information
                        $roomData[$username] = $room;
                    }
                }

                // Display both tables in the same block
                echo '<div style="display: flex; justify-content: space-around;">';

                // Display the merged data in a table
                echo '<div>';
                echo '<h3> </h3>';
                echo '<table class="tbl-content">';
                echo '<thead>';
                echo '<tr>';
                echo '<th style="padding: 10px;">Name</th>';
                echo '<th style="padding: 10px;">ID Number</th>';
                echo '<th style="padding: 10px;">Sex</th>';
                echo '<th style="padding: 10px;">Contact</th>';
                echo '<th style="padding: 10px;">Emergency Contact</th>';
                echo '<th style="padding: 10px;">Medical History</th>';
                echo '<th style="padding: 10px;">Timestamp</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($mergedData as $userData) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($userData['username']) . '</td>';
                    echo '<td>' . implode(', ', $userData['serialnumbers']) . '</td>';
                    echo '<td>' . htmlspecialchars($userData['sex']) . '</td>';
                    echo '<td>' . htmlspecialchars($userData['Contact']) . '</td>';
                    echo '<td>' . htmlspecialchars($userData['EmergencyContact']) . '</td>';
                    echo '<td>' . htmlspecialchars($userData['MedicalHistory']) . '</td>';
                    echo '<td>' . htmlspecialchars($userData['timestamp']) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                // Display the room data in a second table
                echo '<div>';
                echo '<h3> </h3>';
                echo '<table class="tbl-content">';
                echo '<thead>';
                echo '<tr>';
                echo '<th style="padding: 10px;">Room</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($roomData as $username => $room) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($room) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                echo '</div>'; // Close the flex container
                ?>


            </fieldset>
        </form>
    </div>

    <!-- map 1 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

    <div id="map1-container">
        <section id="map1" aria-label="Map1" role="region" position="absolute">
            <!-- Your map content for map 1 -->
        </section>
    </div>

    <!-- map 2 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

    <div id="map2-container">
        <section id="map2" aria-label="Map2" role="region" position="absolute">
            <!-- Your map content for map 2 -->
        </section>
    </div>



    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>Vector Tiles in Leaflet JS</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.css" rel="stylesheet" />
    <script src="https://cdn.maptiler.com/leaflet-maptilersdk/v1.0.0/leaflet-maptilersdk.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <style>
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
    </style>

    <script>
        // Declare markers as a global variable
        var markersMap1 = [];
        var markersMap2 = [];

        const map1 = initializeMap('map1', [7.06569722, 125.59678861], mapOptions);
        const map2 = initializeMap('map2', [7.06569722, 125.59678861], mapOptions);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Your Map 1 layers and features... ////////////////////////////////////////////////////////////////////////////////////////////////

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


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// Your Map 2 layers and features... ////////////////////////////////////////////////////////////////////////////////////////////////

        // GeoJSON data
        var myGeoJSON = {
          "type": "FeatureCollection",
          "features": [
            {
              "type": "Feature",
              "geometry": {
                "type": "Polygon",
                "coordinates": [
                  [125.59657807, 7.06560118],
                  [125.59664244, 7.06578485],
                  [125.596739, 7.06582212],
                  [125.59689055, 7.06576622],
                  [125.59679533, 7.06551734],
                  [125.59657807, 7.06560118]
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
                  [125.59648312, 7.06572744],
                  [125.59652759, 7.06584043],
                  [125.5964658, 7.06585894],
                  [125.59640002, 7.06583076],
                  [125.59637449, 7.06576338],
                  [125.59648312, 7.06572744]
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
                  [125.59633693, 7.06566041],
                  [125.59644047, 7.06562166],
                  [125.59648312, 7.06572744],
                  [125.59637449, 7.06576338],
                  [125.59633693, 7.06566041]
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        const csvFilePath = 'css/final_predicted_values_aggregated.csv?' + Date.now();

        updateCSV(map1);
        updateCSV(map2);

        setInterval(() => {
            updateCSV(map1);
            updateCSV(map2);
        }, 5000);

        async function initializeMap(mapId, center, options) {
            const map = L.map(mapId, options).setView(center, 14);
            L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${key}`, {
                tileSize: 512,
                zoomOffset: -1,
                attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
                crossOrigin: true
            }).addTo(map);

            L.control.scale({
                metric: true,
                imperial: false,
                position: 'topright'
            }).addTo(map);

            return map;
        }

        async function updateCSV(map) {
            console.log('Updating CSV...');
            try {
                const response = await fetch(csvFilePath);
                if (response.ok) {
                    const data = await response.text();

                    // Parse CSV data and add markers
                    const csvData = Papa.parse(data, { header: true });
                    addMarkers(map, csvData.data);
                } else {
                    console.error('Failed to fetch CSV:', response.status, response.statusText);
                }
            } catch (error) {
                console.error('Error fetching CSV:', error);
            }
        }

        async function addMarkers(map, data) {
            // Clear existing markers
            map.eachLayer((layer) => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            const markerGroups = {};

            await Promise.all(data.map(async (row) => {
                const lat = parseFloat(row.lat);
                const lng = parseFloat(row.lng);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const userName = await fetchUserNameFromDatabase(row.ssid);

                    const key = `${lat}_${lng}`;

                    if (!markerGroups[key]) {
                        markerGroups[key] = {
                            marker: L.marker([lat, lng], { icon: customIconStyle }),
                            userNames: [userName],
                            ssids: [row.ssid],
                        };
                    } else {
                        markerGroups[key].userNames.push(userName);
                        markerGroups[key].ssids.push(row.ssid);
                    }
                }
            }));

            Object.values(markerGroups).forEach((markerGroup) => {
                const userNamesList = markerGroup.userNames.join(', ');
                const ssidsList = markerGroup.ssids.join(', ');

                const popupContent = `<b>${userNamesList}</b><br>SSID: ${ssidsList}<br>Latitude: ${markerGroup.marker.getLatLng().lat}<br>Longitude: ${markerGroup.marker.getLatLng().lng}`;

                markerGroup.marker.bindPopup(popupContent);
                map.addLayer(markerGroup.marker);
            });
        }

        async function fetchUserNameFromDatabase(ssid) {
            try {
                const response = await fetch(`map_fetchUsername.php?ssid=${encodeURIComponent(ssid)}`);
                const data = await response.json();

                if (data.error) {
                    console.error('Error fetching user name:', data.error);
                    return 'Unknown User';
                }

                return data.userName;
            } catch (error) {
                console.error('Error fetching user name:', error);
                return 'Unknown User';
            }
        }

    </script>



</body>
</html>

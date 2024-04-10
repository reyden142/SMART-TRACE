<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';

// Assuming $conn is the database connection variable

// Array to store CSV file paths
$csvFiles = [
    'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/final_predicted_values_aggregated_map1.csv',
    'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/final_predicted_values_aggregated_map2.csv'
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>Map</title>

    <!-- Include Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <!-- Include other CSS files -->
    <link rel="stylesheet" type="text/css" href="css/map.css">

    <!-- Include jQuery and Bootstrap JS -->
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>

</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <!-- Your existing HTML content -->
    <h1 class="slideInDown animated">Map</h1>
    <div class="form-style-5 slideInDown animated">
        <form enctype="multipart/form-data">
            <div class="alert_user"></div>
            <fieldset>
                <legend><span class="number">1</span> Online User</legend>

        <?php
        // Your PHP code for processing CSV data and displaying tables
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
                                #'timestamp' => $row[2], // Assuming timestamp is in the 6th column (adjust if needed)
                                'username' => $username,
                                'room' => $row[2],
                            ];
                        }
                    }
                }

                // Display both tables in the same block
                echo '<div style="display: flex; justify-content: space-around;">';


                // Display the merged data in a table

                echo '<div">';
                echo '<h1> </h1>';
                echo '<table class="tbl-content">';
                echo '<thead>';
                echo '<tr>';
                echo '<th style="padding: 10px;">Name</th>';
                echo '<th style="padding: 10px;">ID Number</th>';
                echo '<th style="padding: 10px;">Sex</th>';
                echo '<th style="padding: 10px;">Contact</th>';
                echo '<th style="padding: 10px;">Emergency Contact</th>';
                echo '<th style="padding: 10px;">Medical History</th>';
                #echo '<th style="padding: 10px;">Timestamp</th>';
                echo '<th style="padding: 10px;">Room</th>';
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
                    #echo '<td>' . htmlspecialchars($userData['timestamp']) . '</td>';
                    echo '<td>' . htmlspecialchars($userData['room']) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                // Display the room data in a second table
                echo '<div>';
                echo '<h3> </h3>';
                echo '<table class="tbl-content">';
                echo '<tbody>';


                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                echo '</div>'; // Close the flex container
        ?>
            </fieldset>
        </form>
    </div>


        <!-- Display tables side by side -->
        <div style="display: none; justify-content: space-around;">

            <!-- Table 1: Display merged data -->
            <div>
                <h3>Table 1</h3>
                <table class="tbl-content">
                    <!-- ... (Your existing table 1 code) ... -->
                </table>
            </div>

            <!-- Table 2: Display room data -->
            <div>
                <h3>Table 2</h3>
                <table class="tbl-content">
                    <!-- ... (Your existing table 2 code) ... -->
                </table>
            </div>

        </div>


    <!-- Count -->

    <div class="count_rectangle">

        <script>
          window.console = window.console || function(t) {};
        </script>

    <?php
    // Path to the CSV file
    $csvFilePath = 'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/room_counts.csv';

    // Read the CSV file into an associative array
$csvData = array_map('str_getcsv', file($csvFilePath));
$header = array_shift($csvData);
$csvAssociative = array();
foreach ($csvData as $row) {
    // Check if the row has the same number of elements as the header
    if (count($row) === count($header)) {
        $csvAssociative[] = array_combine($header, $row);
    } else {
        // Handle the error or log it

    }
}


    // Extract counts for each room
    $firstFloorCount = $csvAssociative[3]['Count'];
    $secondFloorCount = $csvAssociative[4]['Count'];
    $be111Count = $csvAssociative[0]['Count'];
    $be213Count = $csvAssociative[1]['Count'];
    $be216Count = $csvAssociative[2]['Count'];
    $totalPersons = $csvAssociative[5]['Count'];
    ?>

    <div style="count">
        <div translate="no">
            <section class="charts_orb">
                <article class="orb">
                    <div class="orb_graphic">
                        <svg>
                            <circle class="fill"></circle>
                            <circle class="progress"></circle>
                        </svg>
                        <div class="orb_value count"><?= $totalPersons ?></div>
                    </div>
                    <div class="orb_label">
                        BE Building
                    </div>
                </article>

                <?php
                $floorCounts = array(
                    array('label' => 'First Floor', 'count' => $firstFloorCount),
                    array('label' => '2nd Floor', 'count' => $secondFloorCount),
                    array('label' => 'BE 111', 'count' => $be111Count),
                    array('label' => 'BE 213', 'count' => $be213Count),
                    array('label' => 'BE 216', 'count' => $be216Count),
                );

                foreach ($floorCounts as $floor) {
                ?>
                    <article class="orb">
                        <div class="orb_graphic">
                            <svg>
                                <circle class="fill"></circle>
                                <circle class="progress" style="stroke-dasharray: calc((<?= $floor['count'] ?> / <?= $totalPersons ?>) * 200) 200;"></circle>
                            </svg>
                            <div class="orb_value count"><?= $floor['count'] ?></div>
                        </div>
                        <div class="orb_label">
                            <?= $floor['label'] ?>
                        </div>
                    </article>
                <?php
                }
                ?>

            </section>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
            <script id="rendered-js">
                $(document).ready(function () {
                    // Get all elements with the 'count' class
                    var countElements = document.querySelectorAll('.count');

                    // Set up the Intersection Observer
                    var observer = new IntersectionObserver(function (entries, observer) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                // Animate the counter when the element is in the viewport
                                $(entry.target).prop('Counter', 0).animate({
                                    Counter: $(entry.target).text()
                                },
                                {
                                    duration: 1500,
                                    easing: 'linear',
                                    step: function (now) {
                                        $(entry.target).text(Math.ceil(now));
                                    }
                                });
                                observer.unobserve(entry.target); // Stop observing once animation is triggered
                            }
                        });
                    }, { threshold: 0.5 }); // Adjust the threshold as needed

                    // Observe each element with the 'count' class
                    countElements.forEach(function (element) {
                        observer.observe(element);
                    });
                });
            </script>

            <script>
                // Function to update the CSV data and refresh the content
                function updateCSV() {
                    $.ajax({
                        url: 'map_room_counts_csv_updated.php', // Replace with the actual path to your server-side script
                        type: 'GET',
                        success: function (data) {
                            console.log('Room Counts CSV file updated');
                            // Refresh the content on success (you may want to update only specific parts)
                            $('#orb-section').load('your-page.php #orb-section');
                        },
                        error: function (xhr, status, error) {
                            console.error('Error updating CSV:', error);
                        }
                    });
                }

                // Update the CSV every 5 seconds
                setInterval(updateCSV, 5000);
            </script>
        </div>
    </div>

    <!-- Animation for the Circles starts once it shows in the screen
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the circle element for progress animation
            const circleProgress = document.querySelector('.charts_orb svg circle.progress');

            // Set up the Intersection Observer
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Add the class to trigger the animation
                        circleProgress.classList.add('animate');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            // Start observing the element
            observer.observe(circleProgress);
        });
    </script>
    -->

      <!-- Map 1 -->
    <div style="width: 50%; float: left;">
        <section id="map1" aria-label="Map1" role="region" position="absolute" >
            <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
            <p><a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a></p>
        </section>
        </div>

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
        //const key = 'aF7HhncV5bhT2pqqWdRV';
        const map1 = L.map('map1', {
            preferCanvas: true
        }).setView([7.06569722, 125.59678861], 14);

        // Set the maxZoom and minZoom properties
        map1.options.maxZoom = 25; // Adjust this value as needed for your requirements
        map1.options.minZoom = 17; // Adjust this value as needed


        L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=aF7HhncV5bhT2pqqWdRV`, {
            tileSize: 512,
            zoomOffset: -1,
            attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
            crossOrigin: true
        }).addTo(map1);

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


        var geojsonLayer = L.geoJSON(myGeoJSON).addTo(map1);

        // Bind a popup to the GeoJSON layer
       // geojsonLayer.bindPopup("<b>BE Building</b>");


        var bounds = [Infinity, Infinity, -Infinity, -Infinity];

        L.control.scale({
            metric: true,
            imperial: false,
            position: 'topright'
        }).addTo(map1);

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

        // Load the final_predicted_values_aggregated.csv file with a timestamp to prevent caching
        const csvFilePath = 'css/final_predicted_values_aggregated_map1.csv?' + Date.now();

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

        async function addMarkers(data) {
          // Clear existing markers
          map1.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
              map1.removeLayer(layer);
            }
          });

          const markerGroups = {};

          for (const row of data) {
            const lat = parseFloat(row.lat);
            const lng = parseFloat(row.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
              // Fetch user name based on MAC address from the database
              const userName = await fetchUserNameFromDatabase(row.ssid);

              const key = `${lat}_${lng}`;

              if (!markerGroups[key]) {
                // Create a new marker group for this location
                markerGroups[key] = {
                  marker: L.marker([lat, lng], { icon: customIconStyle }),
                  userNames: [userName],
                  ssids: [row.ssid],
                };
              } else {
                // Add user name and SSID to existing marker group
                markerGroups[key].userNames.push(userName);
                markerGroups[key].ssids.push(row.ssid);
              }
            }
          }

          // Add all marker groups to the map
          Object.values(markerGroups).forEach((markerGroup) => {
            const userNamesList = markerGroup.userNames.join(', ');
            const ssidsList = markerGroup.ssids.join(', ');

            const popupContent = `<b>${userNamesList}</b><br>SSID: ${ssidsList}<br>Latitude: ${markerGroup.marker.getLatLng().lat}<br>Longitude: ${markerGroup.marker.getLatLng().lng}`;

            markerGroup.marker.bindPopup(popupContent);
            map1.addLayer(markerGroup.marker);
          });
        }

        // Function to fetch user name from the database based on MAC address
        async function fetchUserNameFromDatabase(ssid) {
          try {
            const response = await fetch(`map1_fetchUsername.php?ssid=${encodeURIComponent(ssid)}`);
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
          console.log('Updating MAP1 CSV...');
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
        setInterval(updateCSV, 5000);

        // Include PapaParse library for CSV parsing
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js';
        script.onload = updateCSV; // Call updateCSV when PapaParse is loaded
        document.head.appendChild(script);

        const markerClusterScript = document.createElement('script');
        markerClusterScript.src = 'https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js';
        document.head.appendChild(markerClusterScript);

    </script>


    <!-- Map 2 -->

        <section id="map2" aria-label="Map2" role="region" position="absolute" >
            <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
            <p><a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a></p>
        </section>


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
        //const key = 'aF7HhncV5bhT2pqqWdRV';
        const map2 = L.map('map2', {
            preferCanvas: true
        }).setView([7.06569722, 125.59678861], 14);

        // Set the maxZoom and minZoom properties
        map2.options.maxZoom = 25; // Adjust this value as needed for your requirements
        map2.options.minZoom = 17; // Adjust this value as needed


        L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=aF7HhncV5bhT2pqqWdRV`, {
            tileSize: 512,
            zoomOffset: -1,
            attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
            crossOrigin: true
        }).addTo(map2);

       // GeoJSON data map2
                var myGeoJSON2 =
                {
                  "type": "FeatureCollection",
                  "features": [
                    {
                      "type": "Feature",
                      "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                          [
                            [125.59637449, 7.06576338],
                            [125.59648312, 7.06572744],
                            [125.59650144, 7.06576752],
                            [125.5965249, 7.06577757],
                            [125.5964806, 7.06586484],
                            [125.59640102, 7.0658299],
                            [125.59637449, 7.06576338]
                          ]
                        ]
                      },
                      "id": "3ea697ba-4906-4119-bac3-28185d4370af",
                      "properties": {
                        "name": "BE111"
                      }
                    },
                    {
                      "type": "Feature",
                      "geometry": {
                        "type": "LineString",
                        "coordinates": [
                          [125.59650144, 7.06576752],
                          [125.5965249, 7.06577757],
                          [125.5964806, 7.06586484]
                        ]
                      },
                      "id": "8d8997bd-e916-476c-b6cf-2348356abf2c",
                      "properties": {
                        "name": ""
                      }
                    },
                    {
                      "type": "Feature",
                      "geometry": {
                        "type": "LineString",
                        "coordinates": [
                          [125.59648312, 7.06572744],
                          [125.59647392, 7.06570462],
                          [125.59636602, 7.06574016]
                        ]
                      },
                      "id": "92d9bef7-74fc-4953-bc47-406bb50bee02",
                      "properties": {
                        "name": ""
                      }
                    },
                    {
                      "type": "Feature",
                      "geometry": {
                        "type": "LineString",
                        "coordinates": [
                          [125.59637449, 7.06576338],
                          [125.59640102, 7.0658299],
                          [125.59664152, 7.06593549]
                        ]
                      },
                      "id": "290edff5-3525-4f0b-81a5-f017953195d5",
                      "properties": {
                        "name": ""
                      }
                    },
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
                    },
                    {
                      "type": "Feature",
                      "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                          [
                            [125.59640102, 7.0658299],
                            [125.59633693, 7.06566041],
                            [125.59632563, 7.06563214],
                            [125.59634798, 7.06556914],
                            [125.59638016, 7.06556116],
                            [125.59639536, 7.06551502],
                            [125.59637748, 7.0654467],
                            [125.59684508, 7.06527812],
                            [125.5969926, 7.06567917],
                            [125.59700333, 7.06568627],
                            [125.59703909, 7.06570756],
                            [125.59717946, 7.06565432],
                            [125.59725188, 7.06584864],
                            [125.59698277, 7.0659489],
                            [125.5969935, 7.06597818],
                            [125.59662156, 7.0661166],
                            [125.59658401, 7.066019],
                            [125.59661799, 7.06600391],
                            [125.59664928, 7.06593737],
                            [125.59640102, 7.0658299]
                          ]
                        ]
                      },
                      "id": "24b16770-840f-443d-809b-d176e0706d12",
                      "properties": {
                        "name": ""
                      }
                    },
                    {
                      "type": "Feature",
                      "geometry": {
                        "type": "LineString",
                        "coordinates": [
                          [125.59648312, 7.06572744],
                          [125.59650144, 7.06576752],
                          [125.5965249, 7.06577757],
                          [125.59648093, 7.06586499]
                        ]
                      },
                      "id": "45ba25f5-671c-4ffc-99ac-f7404d45d3e0",
                      "properties": {
                        "name": ""
                      }
                    }
                  ]
                };

                var geojsonLayer = L.geoJSON(myGeoJSON2).addTo(map2);


        // Bind a popup to the GeoJSON layer
       // geojsonLayer.bindPopup("<b>BE Building</b>");


        var bounds = [Infinity, Infinity, -Infinity, -Infinity];

        L.control.scale({
            metric: true,
            imperial: false,
            position: 'topright'
        }).addTo(map2);

        // The CSS to style the custom marker
    var customMarkerStyle2 = `
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


        // Load the final_predicted_values_aggregated.csv file with a timestamp to prevent caching
        const csvFilePath2 = 'css/final_predicted_values_aggregated_map2.csv?' + Date.now();

        // Inject the custom marker CSS into the document
        const style2 = document.createElement('style');
        style2.innerHTML = customMarkerStyle2;
        document.head.appendChild(style2);

        // Create a custom CSS style for the marker
        const customIconStyle2 = L.divIcon({
          className: 'custom-icon',
          html: '<div class="marker-icon" style="background-color: blue;"></div>',
          draggable: true // Enable dragging
        });

        async function addMarkers2(data) {
          // Clear existing markers
          map2.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
              map2.removeLayer(layer);
            }
          });

          const markerGroups2 = {};

          for (const row of data) {
            const lat = parseFloat(row.lat);
            const lng = parseFloat(row.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
              // Fetch user name based on MAC address from the database
              const userName = await fetchUserNameFromDatabase2(row.ssid);

              const key = `${lat}_${lng}`;

              if (!markerGroups2[key]) {
                // Create a new marker group for this location
                markerGroups2[key] = {
                  marker: L.marker([lat, lng], { icon: customIconStyle }),
                  userNames: [userName],
                  ssids: [row.ssid],
                };
              } else {
                // Add user name and SSID to existing marker group
                markerGroups2[key].userNames.push(userName);
                markerGroups2[key].ssids.push(row.ssid);
              }
            }
          }

          // Add all marker groups to the map
          Object.values(markerGroups2).forEach((markerGroup) => {
            const userNamesList = markerGroup.userNames.join(', ');
            const ssidsList = markerGroup.ssids.join(', ');

            const popupContent = `<b>${userNamesList}</b><br>SSID: ${ssidsList}<br>Latitude: ${markerGroup.marker.getLatLng().lat}<br>Longitude: ${markerGroup.marker.getLatLng().lng}`;

            markerGroup.marker.bindPopup(popupContent);
            //map1.addLayer(markerGroup.marker);
            map2.addLayer(markerGroup.marker);
          })
        }

        // Function to fetch user name from the database based on MAC address
        async function fetchUserNameFromDatabase2(ssid) {
          try {
            const response = await fetch(`map2_fetchUsername.php?ssid=${encodeURIComponent(ssid)}`);
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
        async function updateCSV2() {
          console.log('Updating MAP2 CSV...');
          try {
            const response = await fetch(csvFilePath2);
            if (response.ok) {
              const data = await response.text();

              // Parse CSV data and add markers
              const csvData = Papa.parse(data, { header: true });
              addMarkers2(csvData.data);
            } else {
              console.error('Failed to fetch CSV:', response.status, response.statusText);
            }
          } catch (error) {
            console.error('Error fetching CSV:', error);
          }
        }

        // Initial call to load markers
        updateCSV2();

        // Set interval to update markers every 1 second
        setInterval(updateCSV2, 5000);

        // Include PapaParse library for CSV parsing
        const script2 = document.createElement('script');
        script2.src = 'https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js';
        script2.onload = updateCSV; // Call updateCSV when PapaParse is loaded
        document.head.appendChild(script2);

        const markerClusterScript2 = document.createElement('script');
        markerClusterScript2.src = 'https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js';
        document.head.appendChild(markerClusterScript2);


    </script>

</body>

</html>

<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';

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
    header("location: map.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Map</title>

    <link rel="stylesheet" type="text/css" href="css/map.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script src="js/manage_users.js"></script>
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
                    echo '<h2>Data from ' . basename($csvFile) . '</h2>';
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
                        $macAddress = $row[0]; // Assuming MAC address is in the first column

                        // Query the database to retrieve the user's username
                        $query = "SELECT username FROM users WHERE Macaddress = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $macAddress);
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

        <section id="map" aria-label="Map" role="region" style="width: 1200px; height: 1200px; right: 1350px ">
            <a href="https://www.maptiler.com" style="position:absolute;left:10px;bottom:10px;z-index:999;"><img src="https://api.maptiler.com/resources/logo.svg" alt="MapTiler logo"></a>
            <p><a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a></p>
        </section>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Leaflet map initialization
        const map = L.map('map').setView([7.06569722, 125.59678861], 14);
        L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=aF7HhncV5bhT2pqqWdRV`, {
            tileSize: 512,
            zoomOffset: -1,
            minZoom: 20,
            attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
            crossOrigin: true
        }).addTo(map);

        // Add GeoJSON data to the map
        var myGeoJSON = { "type": "FeatureCollection", /* ... Your GeoJSON data ... */ };
        L.geoJSON(myGeoJSON).addTo(map);
    </script>
</body>
</html>

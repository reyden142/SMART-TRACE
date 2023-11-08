<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';

session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: map.php");
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Map</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/RFID_STICKER_LOGO.jpg" href="">

    <link rel="stylesheet" type="text/css" href="css/map.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script src="js/manage_users.js"></script>

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
    </div>
</main>
</body>
</html>

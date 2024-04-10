<?php
// update_marker.php

require 'connectDB.php';

session_start();

// Log received data
error_log("Received data: " . print_r($_POST, true));

// Check if the necessary data is received
if (isset($_POST['title']) && isset($_POST['lat']) && isset($_POST['lng'])) {
    // Sanitize and get the values
    $title = $_POST['title'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    // Perform the database update
    // Assuming your table is named 'markers2_200' and has columns 'latitude' and 'longitude'
    $query = "UPDATE markers2_200 SET `lat` = $lat, `lng` = $lng WHERE title = '$title'";

    // Execute the query using the database connection
    if (mysqli_query($conn, $query)) {
        echo "Marker updated successfully";
    } else {
        echo "Error updating marker in the database: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request. Required data not provided.";
}

// Close the database connection when you are done
mysqli_close($conn);
?>

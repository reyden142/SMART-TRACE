<?php
// delete_marker.php

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

    // Perform the database deletion
    // Assuming your table is named 'markers' and has columns 'latitude' and 'longitude'
    $query = "DELETE FROM markers WHERE title = '$title' AND `lat` = $lat AND `lng` = $lng";

    // Execute the query using the database connection
    if (mysqli_query($conn, $query)) {
        echo "Marker deleted successfully";
    } else {
        echo "Error deleting marker from the database: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request. Required data not provided.";
}

// Close the database connection when you are done
mysqli_close($conn);
?>

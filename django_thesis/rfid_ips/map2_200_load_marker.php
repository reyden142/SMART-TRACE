<?php
// map_load_marker.php

require 'connectDB.php';

session_start();

if (isset($_POST['loadButton'])) {
    // Perform the database query to select all markers
    $query = "SELECT * FROM markers2_200";

    // Execute the query using the database connection
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Fetch all rows as an associative array
        $markers = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Iterate through markers and set a default title if it is null
        foreach ($markers as &$marker) {
            if ($marker['title'] === null) {
                $marker['title'] = "Default Title";
            }
        }

        // Convert the array to JSON and echo the response
        echo json_encode($markers);
    } else {
        echo "Error loading markers from the database: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request. Required data not provided.";
}

// Close the database connection when you are done
mysqli_close($conn);
?>

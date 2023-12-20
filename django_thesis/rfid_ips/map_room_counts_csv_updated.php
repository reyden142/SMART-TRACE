<?php

// Define the CSV file path
$csvFilePath = 'css/room_counts.csv';

// Open the CSV file in write mode
$csvFile = fopen($csvFilePath);

// Write header to CSV file
fputcsv($csvFile, ['RoomID', 'Count']);

// Write data to CSV file
foreach ($roomCounts as $roomid => $count) {
    fputcsv($csvFile, [$roomid, $count]);
}

// Close the CSV file
fclose($csvFile);

// Output a message indicating the update
echo 'CSV file updated successfully';

?>
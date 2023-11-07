<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: map.php");
}
?>

<?php

/* header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = file_get_contents("php://input");
$decoded_data = json_decode($data, true);

if ($decoded_data) {
    $file = "rssi_data.txt";

    foreach ($decoded_data as $entry) {
        $mac_address = $entry["mac_address"];
        $rssi = $entry["rssi"];

        $data_to_write = "MAC Address: $mac_address, RSSI: $rssi dBm\n";
        file_put_contents($file, $data_to_write, FILE_APPEND);
    }

    echo json_encode(["message" => "Data received and processed."]);
} else {
    echo json_encode(["message" => "Failed to decode the data."]);
} */
?>

<?php include'header.php';?>
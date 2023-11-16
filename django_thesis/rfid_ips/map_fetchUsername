<?php
// Include the database connection code from 'connectDB.php'
require 'connectDB.php';

// Check if the Macaddress parameter is set in the request
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
} else {
    // Handle the case where Macaddress is not provided
    header('Content-Type: application/json');
    echo json_encode(['error' => 'MAC address not provided']);
}
?>

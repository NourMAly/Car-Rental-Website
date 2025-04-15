<?php
// data.php
error_reporting(0);
ini_set('display_errors', 0);

// Database connection
$conn = new mysqli("localhost", "root", "", "carrental");

// Check connection
if ($conn->connect_error) {
    echo '<p class="text-danger text-center">Error while connecting to the database. Please try again later.</p>';
    exit();
}

// Fetch office locations
$sql = "SELECT office_no, country, city, address, phone_no FROM office";
$result = $conn->query($sql);

// Create an array to store office data
$offices = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $offices[] = $row;
    }
}

$conn->close();

// Return the office data as JSON
echo json_encode($offices);
?>

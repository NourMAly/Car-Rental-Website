<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carrental";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve search parameters from GET
$ssn = $_GET['ssn'] ?? '';
$fname = $_GET['fname'] ?? '';
$lname = $_GET['lname'] ?? '';
$gender = $_GET['gender'] ?? '';
$email = $_GET['email'] ?? '';
$phone_no = $_GET['phone_no'] ?? '';

// Build the query
$sql = "SELECT * FROM customer WHERE 1=1"; // Fetch all customers by default
if (!empty($ssn)) $sql .= " AND ssn = '$ssn'";
if (!empty($fname)) $sql .= " AND fname LIKE '%$fname%'";
if (!empty($lname)) $sql .= " AND lname LIKE '$%lname%'";
if (!empty($gender)) $sql .= " AND gender = '$gender'";
if (!empty($email)) $sql .= " AND email = '$email'";
if (!empty($phone_no)) $sql .= " AND phone_no = '$phone_no'";

// Execute the query and handle errors
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Start the result display
if ($result->num_rows > 0) {
    echo "<div class='results-container'>";
    echo "<h3>Search Results</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='result-item'>";
        echo "<p><strong>SSN:</strong> " . htmlspecialchars($row['ssn']) . "</p>";
        echo "<p><strong>First Name:</strong> " . htmlspecialchars($row['fname']) . "</p>";
        echo "<p><strong>Last Name:</strong> " . htmlspecialchars($row['lname']) . "</p>";
        echo "<p><strong>Gender:</strong> " . htmlspecialchars($row['gender']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
        echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($row['phone_no']) . "</p>";
        echo "</div><hr>";
    }
    echo "</div>";
} else {
    echo "<div class='no-results-message'><p>No results found.</p></div>";
}

// Close the database connection
$conn->close();
?>

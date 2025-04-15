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
$class = $_GET['class'] ?? '';
$model = $_GET['model'] ?? '';
$year = $_GET['year'] ?? '';
$color = $_GET['color'] ?? '';
$brand = $_GET['brand'] ?? '';

// Build the query
$sql = "SELECT * FROM car WHERE 1=1"; // Fetch all cars by default
if (!empty($class)) $sql .= " AND class = '$class'";
if (!empty($model)) $sql .= " AND model LIKE '%$model%'";
if (!empty($year)) $sql .= " AND year = '$year'";
if (!empty($color)) $sql .= " AND color LIKE '%$color%'";
if (!empty($brand)) $sql .= " AND brand LIKE '%$brand%'";

// Execute the query and handle errors
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

echo "<h3>Search Results</h3>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      if (!empty($row['photo'])) {
        // Assuming the 'photo' column contains the image path relative to the web server root
        echo "<img src='" . htmlspecialchars($row['photo']) . "' alt='Car Image' style='max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 10px;'>";
    } else {
        echo "<p><em>No image available</em></p>";
    }
      
        echo "<div class='result-item'>";
        echo "<p><strong>Brand:</strong> " . htmlspecialchars($row['brand']) . "</p>";
        echo "<p><strong>Model:</strong> " . htmlspecialchars($row['model']) . "</p>";
        echo "<p><strong>Year:</strong> " . htmlspecialchars($row['year']) . "</p>";
        echo "<p><strong>Color:</strong> " . htmlspecialchars($row['color']) . "</p>";
        echo "<p><strong>Class:</strong> " . htmlspecialchars($row['class']) . "</p>";
        echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
        echo "</div><hr>";
    }
} else {
    echo "<p>No results found.</p>";
}

// Close the database connection
$conn->close();
?>

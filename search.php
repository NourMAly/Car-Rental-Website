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
$status = "available"; // Only fetch cars with status "active"

// Build the query
$sql = "SELECT * FROM car WHERE status = '$status'";
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Results</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url("background.png") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }

    .results-container {
      max-width: 800px;
      margin: 50px auto;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      color: #343a40;
    }

    h3 {
      text-align: center;
      margin-bottom: 20px;
    }

    .result-item {
      margin-bottom: 20px;
    }

    .result-item img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="results-container">
    <h3>Search Results</h3>
    <?php
    // Update the image path to be relative for the browser
    function convertToWebPath($absolutePath) {
        $projectRoot = "C:/xampp/htdocs/Final-Project/";
        $relativePath = str_replace($projectRoot, '', $absolutePath);
        return $relativePath;
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='result-item'>";

            // Convert absolute path to relative and display the car image
            if (!empty($row['photo'])) {
                $imagePath = convertToWebPath($row['photo']);
                echo "<img src='" . htmlspecialchars($imagePath) . "' alt='Car Image'>";
            } else {
                echo "<p><em>No image available</em></p>";
            }

            // Display other car details
            echo "<p><strong>Brand:</strong> " . htmlspecialchars($row['brand']) . "</p>";
            echo "<p><strong>Model:</strong> " . htmlspecialchars($row['model']) . "</p>";
            echo "<p><strong>Year:</strong> " . htmlspecialchars($row['year']) . "</p>";
            echo "<p><strong>Color:</strong> " . htmlspecialchars($row['color']) . "</p>";
            echo "<p><strong>Class:</strong> " . htmlspecialchars($row['class']) . "</p>";
            echo "<hr>";
            echo "</div>";
        }
    } else {
        echo "<p>No results found.</p>";
    }

    // Close the database connection
    $conn->close();
    ?>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

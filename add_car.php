<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "carrental");

if ($conn->connect_error) {
    echo "<script>
            alert('Connection failed: " . addslashes($conn->connect_error) . "');
          </script>";
    exit(); // Stop further execution if connection fails
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plate_id = $_POST['plate_id'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $status = $_POST['status'];
    $color = $_POST['color'];
    $brand = $_POST['brand'];
    $class = $_POST['class'];
    $daily_price = $_POST['price'];
    $office_no = $_POST['office_no'];

    // Handle file upload for the car image
    $photo_path = $_FILES['image']['name'];
    $target_dir = "./uploads/";  // Upload directory
    $target_file = $target_dir . basename($photo_path);

    // Move the uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $photo = $target_file;  // Store the file path in the database
    } else {
        echo "<script>
                alert('Sorry, there was an error uploading your file.');
              </script>";
        exit; // Stop further execution if file upload fails
    }

    // Insert data into the car table
    $sql = "INSERT INTO car (plate_id, model, year, status, color, brand, class, photo, daily_price, office_no)
            VALUES ('$plate_id', '$model', '$year', '$status', '$color', '$brand', '$class', '$photo', '$daily_price', '$office_no')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('New car added successfully!');
                window.location.href = 'manage_cars.html'; // Redirect after success
              </script>";
    } else {
        echo "<script>
                alert('Error: " . addslashes($conn->error) . "');
                window.location.href = 'manage_cars.html'; // Redirect after error
              </script>";
    }
}

$conn->close();
?>

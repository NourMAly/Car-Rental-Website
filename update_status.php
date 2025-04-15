<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "carrental");

if ($conn->connect_error) {
    echo "<script>
            alert('Connection failed: " . addslashes($conn->connect_error) . "');
            window.location.href = 'manage_cars.html';  // Redirect after alert
          </script>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plate_id = $_POST['plate_id'];
    $status = $_POST['status'];

    // Check if the car with the given plate_id exists
    $sql = "SELECT * FROM car WHERE plate_id = '$plate_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Car exists, update the status
        $sql = "UPDATE car SET status = '$status' WHERE plate_id = '$plate_id'";
        if ($conn->query($sql) === TRUE) {
            // Show success message and redirect
            echo "<script>
                    alert('Car status updated successfully!');
                    window.location.href = 'manage_cars.html';  // Redirect after alert
                  </script>";
            exit();
        } else {
            // Show error message and redirect
            echo "<script>
                    alert('Error updating car status: " . addslashes($conn->error) . "');
                    window.location.href = 'manage_cars.html';  // Redirect after alert
                  </script>";
            exit();
        }
    } else {
        // Car with the given plate_id does not exist
        echo "<script>
                alert('Car with plate ID " . addslashes($plate_id) . " does not exist.');
                window.location.href = 'manage_cars.html';  // Redirect after alert
              </script>";
        exit();
    }
}

$conn->close();
?>

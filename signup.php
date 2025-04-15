<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carrental"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $ssn = mysqli_real_escape_string($conn, $_POST['ssn']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']); // Gender field
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']); // Phone number field

    // Validate email format
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        echo "<script>alert('Invalid email format!'); window.history.back();</script>";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashed_password = md5($password);

    // Check for duplicate SSN, email
    $check_query = "SELECT * FROM customer WHERE ssn = '$ssn' OR email = '$email'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "<script>alert('SSN or email already exists!'); window.history.back();</script>";
        exit;
    }

    // Insert new customer into the database
    $insert_query = "INSERT INTO customer (ssn, fname, lname, gender, email, password, phone_no) 
                     VALUES ('$ssn', '$fname', '$lname', '$gender', '$email', '$hashed_password', '$phone_no')";

    if ($conn->query($insert_query) === TRUE) {
        // Redirect to user menu page
        header("Location: login.html");
        exit;
    } else {
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

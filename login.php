<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carrental"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['username']; // Email used for login
    $password = $_POST['password']; // Password entered by the user
    $loginType = $_POST['loginType']; // 'user' or 'admin'

    // Validate email format
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        echo "<script>alert('Invalid email format!'); window.history.back();</script>";
        exit;
    }

    if ($loginType === 'admin') {
        // Query to check if the admin exists in the admin table
        $query = "SELECT * FROM admin WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Directly compare the plain text password
        if ($user && md5($password) == md5($user['password'])) {
            // Admin login successful
            $_SESSION['admin_id'] = $user['admin_id']; // Store admin_id in session
            $_SESSION['email'] = $user['email']; // Store email in session
            $_SESSION['role'] = 'admin'; // Set role to admin

            echo "<script>
                    window.location.href = 'admin_home.php'; // Redirect to admin home page
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Invalid email or password for admin.');
                    window.history.back(); // Redirect to login page
                  </script>";
        }

    } else if ($loginType === 'user') {
        // Query to check if the customer exists in the user table
        $query = "SELECT * FROM customer WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email); // Assuming email is used for the customer login as well
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && md5($password) == $user['password']) {
            // Customer login successful
            $_SESSION['ssn'] = $user['ssn']; // Store user_id in session
            $_SESSION['email'] = $user['email']; // Store username in session
            $_SESSION['role'] = 'user'; // Set role to user

            echo "<script>
                    window.location.href = 'usermenu.html'; // Redirect to customer home page
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Invalid email or password for customer.');
                    window.history.back(); // Redirect to login page
                  </script>";
        }
    }
}
?>

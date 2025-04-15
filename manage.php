<?php
// Database connection settings
$servername = "localhost";  // or your server IP
$username = "root";         // your database username
$password = "";             // your database password
$dbname = "carrental";     // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to select customer data
$sql = "SELECT * FROM customer";  // Replace 'customers' with your actual table name
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers Page - Car Rental System</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: url("background.png") no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        /* Navigation Bar */
        nav {
            background-color: #022454;
            padding: 10px 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
        }

        nav ul li {
            flex: 1;
            text-align: center;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            display: block;
            padding: 10px;
            transition: background 0.3s;
        }

        nav ul li a:hover {
            background-color: #575757;
        }

        /* White Container */
        .white-container {
            background-color: white;
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            height: 400px; /* Adjust the height as desired */
            position: relative; /* Enable positioning of child elements */
        }

        .white-container h1 {
            position: absolute; /* Position relative to the container */
            top: 20px; /* Adjust the top offset */
            left: 50%; /* Center horizontally */
            transform: translateX(-50%); /* Fine-tune horizontal centering */
            font-size: 2rem; /* Adjust font size */
            color: #022454; /* Match the theme */
            text-decoration: underline;
        }

        /* Footer Section */
        footer {
            text-align: center;
            background-color: #022454;
            color: white;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #022454;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
        <li><a href="admin_home.php">Home</a></li>
            <li><a href="manage.php">Manage Customers</a></li>
            <li><a href="manage_cars.html">Manage Cars</a></li>
            <li><a href="generate_reports.php">Reports</a></li>
            <li><a href="admin_search.html">Search</a></li>
            <li><a href="menu.html">Logout</a></li>
        </ul>
    </nav>

    <!-- White Container -->
    <div class="white-container">
        <h1>Customer Information</h1>
        
        <!-- Customer Data Table -->
        <table>
            <tr>
                <th>SSN</th>
                <th>Fname</th>
                <th>Lname</th>
                <th>Email</th>
                <th>Phone</th>
                <th>gender</th>
            </tr>

            <?php
            // Check if there are records in the database
            if ($result->num_rows > 0) {
                // Loop through and display each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["ssn"] . "</td>
                            <td>" . $row["fname"] . "</td>
                            <td>" . $row["lname"] . "</td>
                            <td>" . $row["email"] . "</td>
                            <td>" . $row["phone_no"] . "</td>
                            <td>" . $row["gender"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No customers found</td></tr>";
            }
            ?>

        </table>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Car Rental System. All Rights Reserved.
    </footer>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>

</html>

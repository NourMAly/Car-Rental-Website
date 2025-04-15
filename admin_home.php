<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carrental";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home Page - Car Rental System</title>
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
    position: relative; /* Enable positioning of child elements */
}

.white-container h1 {
    position: relative; /* Changed from absolute to relative */
    top: 0; /* Remove unnecessary top offset */
    margin-bottom: 20px; /* Add some space below the heading */
    font-size: 2rem; /* Keep the font size */
    color: #022454; /* Match the theme */
    text-decoration: underline;
    text-align: center; /* Center the text */
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
        footer {
            background-color: #022454;
            padding: 10px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
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
        <h1>Welcome to Admin Dashboard</h1>
        <!-- Automatically Display the Status of All Cars on Today's Date -->
<?php
// Get the current date from the system
$status_date = date('Y-m-d');

// Query to fetch the status of all cars for today's date
$query = "
    SELECT 
        car.car_id, 
        car.plate_id, 
        car.model, 
        car.year, 
        car.daily_price, 
        CASE 
            WHEN NOT EXISTS (
                SELECT 1 
                FROM rent 
                WHERE car.car_id = rent.car_id 
                AND '$status_date' BETWEEN rent.pickup AND rent.return
            ) THEN 'Available'
            ELSE 'Reserved'
        END AS status
    FROM car
    LEFT JOIN rent ON car.car_id = rent.car_id
    GROUP BY car.car_id, car.plate_id, car.model, car.year, car.daily_price";

$result = mysqli_query($conn, $query);

if ($result) {
    echo "<h3>Car Status on $status_date</h3>";
    echo "<table style='width: 100%; border-collapse: collapse; text-align: left;'>
            <thead>
                <tr style='background-color: #f2f2f2;'>
                    <th style='padding: 10px; border-bottom: 1px solid #ddd;'>Car ID</th>
                    <th style='padding: 10px; border-bottom: 1px solid #ddd;'>Plate ID</th>
                    <th style='padding: 10px; border-bottom: 1px solid #ddd;'>Model</th>
                    <th style='padding: 10px; border-bottom: 1px solid #ddd;'>Year</th>
                    <th style='padding: 10px; border-bottom: 1px solid #ddd;'>Daily Price</th>
                    <th style='padding: 10px; border-bottom: 1px solid #ddd;'>Status</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . $row['car_id'] . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . $row['plate_id'] . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . $row['model'] . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . $row['year'] . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>$" . number_format($row['daily_price'], 2) . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; color: " . 
                    ($row['status'] === 'Available' ? 'green' : 'red') . "; font-weight: bold;'>" . 
                    $row['status'] . "</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>Error: " . mysqli_error($conn) . "</p>";
}
?>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Car Rental System. All Rights Reserved.
    </footer>
</body>

</html>
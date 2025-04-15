<?php
// db_connection.php
// Create a connection to the MySQL database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'carrental';  // Correct database name

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports Page</title>
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

        /* Form Styling */
        form {
            margin-bottom: 30px;
        }

        form label {
            display: block;
            margin-bottom: 10px;
        }

        form input[type="date"], form input[type="text"], form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        form input[type="submit"] {
            background-color: #022454;
            color: white;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #575757;
        }

        /* Table Styling */
        table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #022454;
            color: white;
        }

        /* Additional Styling */
        h2 {
            margin-top: 40px;
            font-size: 1.5rem;
            color: #022454;
            text-decoration: underline;
        }

        h3 {
            font-size: 1.2rem;
            color: #022454;
        }

        .white-container {
            margin-bottom: 50px;
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

    <div class="white-container">
        <h1>Car Rental Reports</h1>

        <!-- Form 1: All Reservations Within a Period -->
        <h2>All Reservations Within a Period</h2>
        <form method="POST" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" required>
            <input type="submit" name="submit_reservations_period" value="Generate Report">
        </form>

        <?php
        // Process Reservations Report (All reservations within a specified period including car and customer information)
        if (isset($_POST['submit_reservations_period'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            // Updated query to fetch all reservations within the specified period (including car and customer info)
            $query = "SELECT r.rent_id, r.pickup, r.return, r.payment, 
                             c.ssn, c.fname, c.lname, c.email, c.phone_no,
                             car.car_id, car.plate_id, car.model, car.year, car.brand, car.color, car.daily_price 
                      FROM rent r
                      JOIN customer c ON r.ssn = c.ssn 
                      JOIN car ON r.car_id = car.car_id 
                      WHERE r.pickup BETWEEN '$start_date' AND '$end_date'";

            $result = mysqli_query($conn, $query);

            if ($result) {
                echo "<h3>Reservations from $start_date to $end_date</h3>";
                echo "<table><tr><th>Rent ID</th><th>Customer Name</th><th>Customer Email</th><th>Customer Phone</th><th>Car Model</th><th>Plate ID</th><th>Pickup Date</th><th>Return Date</th><th>Payment</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr><td>" . $row['rent_id'] . "</td><td>" . $row['fname'] . " " . $row['lname'] . "</td><td>" . $row['email'] . "</td><td>" . $row['phone_no'] . "</td><td>" . $row['model'] . "</td><td>" . $row['plate_id'] . "</td><td>" . $row['pickup'] . "</td><td>" . $row['return'] . "</td><td>" . $row['payment'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
        ?>

        <!-- Form 2: All Reservations of Any Car Within a Period -->
        <h2>All Reservations of Any Car Within a Period</h2>
        <form method="POST" action="">
            <label for="car_id">Car ID:</label>
            <input type="text" name="car_id" id="car_id" required>
            <label for="start_date_car">Start Date:</label>
            <input type="date" name="start_date_car" id="start_date_car" required>
            <label for="end_date_car">End Date:</label>
            <input type="date" name="end_date_car" id="end_date_car" required>
            <input type="submit" name="submit_reservations_car" value="Generate Report">
        </form>

        <?php
        // Process Car-Specific Reservations Report
        if (isset($_POST['submit_reservations_car'])) {
            $car_id = $_POST['car_id'];
            $start_date = $_POST['start_date_car'];
            $end_date = $_POST['end_date_car'];

            // Updated query to fetch all reservations for a specific car within a specified period
            $query = "SELECT rent.rent_id, rent.pickup, rent.return, rent.payment, 
                             car.car_id, car.plate_id, car.model, car.year, car.brand, car.color, car.daily_price 
                      FROM rent 
                      JOIN car ON rent.car_id = car.car_id 
                      WHERE rent.car_id = '$car_id' 
                      AND rent.pickup BETWEEN '$start_date' AND '$end_date'";

            $result = mysqli_query($conn, $query);

            if ($result) {
                echo "<h3>Reservations for Car ID: $car_id from $start_date to $end_date</h3>";
                echo "<table><tr><th>Rent ID</th><th>Car Model</th><th>Plate ID</th><th>Pickup Date</th><th>Return Date</th><th>Payment</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr><td>" . $row['rent_id'] . "</td><td>" . $row['model'] . "</td><td>" . $row['plate_id'] . "</td><td>" . $row['pickup'] . "</td><td>" . $row['return'] . "</td><td>" . $row['payment'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
        ?>

        <!-- Form 3: The Status of All Cars on a Specific Day -->
<h2>The Status of All Cars on a Specific Day</h2>
<form method="POST" action="">
    <label for="status_date">Date:</label>
    <input type="date" name="status_date" id="status_date" required>
    <input type="submit" name="submit_car_status" value="Generate Report">
</form>

<?php
// Process Car Status Report
if (isset($_POST['submit_car_status'])) {
    $status_date = $_POST['status_date'];

    // Updated query to fetch the status of all cars on a specific day
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
        echo "<table><tr><th>Car ID</th><th>Plate ID</th><th>Model</th><th>Year</th><th>Daily Price</th><th>Status</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $row['car_id'] . "</td>
                    <td>" . $row['plate_id'] . "</td>
                    <td>" . $row['model'] . "</td>
                    <td>" . $row['year'] . "</td>
                    <td>" . $row['daily_price'] . "</td>
                    <td>" . $row['status'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

        <!-- Form 4: All Reservations of a Specific Customer -->
        <h2>All Reservations of a Specific Customer</h2>
        <form method="POST" action="">
            <label for="customer_ssn">Customer SSN:</label>
            <input type="text" name="customer_ssn" id="customer_ssn" required>
            <input type="submit" name="submit_customer_reservations" value="Generate Report">
        </form>

        <?php
        // Process Customer-Specific Reservations Report
        if (isset($_POST['submit_customer_reservations'])) {
            $ssn = $_POST['customer_ssn'];

            // Updated query to fetch all reservations for a specific customer
            $query = "SELECT rent.rent_id, rent.pickup, rent.return, rent.payment, 
                             customer.fname, customer.lname, car.car_id, car.model, car.plate_id
                      FROM rent
                      JOIN customer ON rent.ssn = customer.ssn
                      JOIN car ON rent.car_id = car.car_id
                      WHERE customer.ssn = '$ssn'";

            $result = mysqli_query($conn, $query);

            if ($result) {
                echo "<h3>Reservations for Customer SSN: $ssn</h3>";
                echo "<table><tr><th>Rent ID</th><th>Customer Name</th><th>Car Model</th><th>Plate ID</th><th>Pickup Date</th><th>Return Date</th><th>Payment</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr><td>" . $row['rent_id'] . "</td><td>" . $row['fname'] . " " . $row['lname'] . "</td><td>" . $row['model'] . "</td><td>" . $row['plate_id'] . "</td><td>" . $row['pickup'] . "</td><td>" . $row['return'] . "</td><td>" . $row['payment'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
        ?>

<!-- Form 5: Daily Payments Within a Specific Period -->
<h2>Daily Payments Within a Specific Period</h2>
<form method="POST" action="">
    <label for="payment_start_date">Start Date:</label>
    <input type="date" name="payment_start_date" id="payment_start_date" required>
    <label for="payment_end_date">End Date:</label>
    <input type="date" name="payment_end_date" id="payment_end_date" required>
    <input type="submit" name="submit_daily_payments" value="Generate Report">
</form>

<?php
// Process Daily Payments Report
if (isset($_POST['submit_daily_payments'])) {
    $start_date = $_POST['payment_start_date'];
    $end_date = $_POST['payment_end_date'];

    // Validate dates
    if ($start_date > $end_date) {
        echo "<p style='color:red;'>Error: Start date must be before or equal to the end date.</p>";
    } else {
        // Initialize an array for daily payments
        $daily_payments = [];
        $current_date = $start_date;

        // Populate daily_payments array with zeros for all dates in the range
        while ($current_date <= $end_date) {
            $daily_payments[$current_date] = 0.0;
            $current_date = date('Y-m-d', strtotime($current_date . '+1 day'));
        }

        // Query to fetch rentals and their corresponding daily prices
        $query = "SELECT r.pickup, r.return, c.daily_price
                  FROM rent r
                  JOIN car c ON r.car_id = c.car_id
                  WHERE r.pickup <= '$end_date' AND r.return >= '$start_date'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pickup = $row['pickup'];
                $return = $row['return'];
                $daily_price = $row['daily_price'];

                // Adjust rental period to overlap with the specified range
                $effective_start = max($start_date, $pickup);
                $effective_end = min($end_date, $return);

                // Calculate the number of effective days
                $effective_days = (strtotime($effective_end) - strtotime($effective_start)) / 86400 + 1;

                // Calculate daily payments for the effective days
                $current_date = $effective_start;
                while ($current_date <= $effective_end) {
                    $daily_payments[$current_date] += $daily_price;
                    $current_date = date('Y-m-d', strtotime($current_date . '+1 day'));
                }
            }
        } else {
            echo "<p>Error fetching rental data: " . mysqli_error($conn) . "</p>";
        }

        // Display the results
        echo "<h3>Daily Payments from $start_date to $end_date</h3>";
        echo "<table>
                <tr>
                    <th>Date</th>
                    <th>Total Payments</th>
                </tr>";
        foreach ($daily_payments as $date => $total) {
            echo "<tr>
                    <td>$date</td>
                    <td>" . number_format($total, 0) . "</td>
                  </tr>";
        }
        echo "</table>";
    }
}
?>



    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Car Rental System. All rights reserved.
    </footer>

</body>

</html>

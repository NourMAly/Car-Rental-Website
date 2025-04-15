<?php
session_start(); // Start the session
if (!isset($_SESSION['ssn'])) {
    // Handle the case where SSN is not set (maybe redirect or show an error)
    echo "SSN is not set in the session.";
    exit; // Exit to prevent further code execution
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Adjust credentials as needed
$dbname = "carrental"; // Your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle car reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id'], $_POST['ssn'], $_POST['pickup'], $_POST['return'])) {
    $car_id = $_POST['car_id'];
    $ssn = $_SESSION['ssn']; // Use the SSN from the session
    $pickup = $_POST['pickup'];
    $return = $_POST['return'];

    // Check if the provided SSN exists in the database
    $query = "SELECT * FROM customer WHERE ssn = :ssn";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ssn', $ssn, PDO::PARAM_INT);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        $message = "The provided SSN does not exist. Please enter a valid SSN.";
    } else {
        // Calculate the number of days rented
        $pickup_date = new DateTime($pickup);
        $return_date = new DateTime($return);
        $interval = $pickup_date->diff($return_date);
        $days_rented = $interval->days;

        // Check if the car is already reserved during the specified period
        $query = "
            SELECT * FROM rent
            WHERE car_id = :car_id
            AND (
                (pickup BETWEEN :pickup AND :return) OR
                (`return` BETWEEN :pickup AND :return) OR
                (:pickup BETWEEN pickup AND `return`) OR
                (:return BETWEEN pickup AND `return`)
            )
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->bindParam(':pickup', $pickup);
        $stmt->bindParam(':return', $return);
        $stmt->execute();
        $existing_reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_reservation) {
            // Check for conflicts by comparing pickup and return dates
            $existing_pickup = new DateTime($existing_reservation['pickup']);
            $existing_return = new DateTime($existing_reservation['return']);

            // Condition for checking if the reservation dates are either after the latest return date or before the pickup date
            if ($return_date > $existing_pickup && $pickup_date < $existing_return) {
                $message = "This car is already reserved for the selected dates.";
            } else {
                // Reservation is possible because dates are either after the latest return or before the pickup
                // Proceed with the reservation logic
                $query = "SELECT daily_price FROM car WHERE car_id = :car_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
                $stmt->execute();
                $car = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($car) {
                    // Calculate the total payment
                    $total_payment = $car['daily_price'] * $days_rented;

                    // Start a transaction
                    $conn->beginTransaction();
                    try {
                        // Update car status to reserved
                        $query = "UPDATE car SET status = 'Reserved' WHERE car_id = :car_id AND status = 'Available'";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            // Insert reservation into rent table, using the calculated total payment
                            $query = "INSERT INTO rent (car_id, ssn, pickup, `return`, payment) VALUES (:car_id, :ssn, :pickup, :return, :payment)";
                            $stmt = $conn->prepare($query);
                            $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
                            $stmt->bindParam(':ssn', $ssn, PDO::PARAM_INT);
                            $stmt->bindParam(':pickup', $pickup);
                            $stmt->bindParam(':return', $return);
                            $stmt->bindParam(':payment', $total_payment);

                            $stmt->execute();
                            $conn->commit();

                            // Redirect to the payment page
                            header("Location: payment.php?car_id=$car_id&total_payment=$total_payment");
                            exit();
                        } else {
                            $conn->rollBack();
                            $message = "Failed to reserve the car. It may no longer be available.";
                        }
                    } catch (Exception $e) {
                        $conn->rollBack();
                        $message = "Error: " . $e->getMessage();
                    }
                } else {
                    $message = "Car not found.";
                }
            }
        } else {
            // If no overlapping reservation exists, proceed
            $query = "SELECT daily_price FROM car WHERE car_id = :car_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
            $stmt->execute();
            $car = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($car) {
                // Calculate the total payment
                $total_payment = $car['daily_price'] * $days_rented;

                // Start a transaction
                $conn->beginTransaction();
                try {
                    // Update car status to reserved
                    $query = "UPDATE car SET status = 'Reserved' WHERE car_id = :car_id AND status = 'Available'";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        // Insert reservation into rent table, using the calculated total payment
                        $query = "INSERT INTO rent (car_id, ssn, pickup, `return`, payment) VALUES (:car_id, :ssn, :pickup, :return, :payment)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
                        $stmt->bindParam(':ssn', $ssn, PDO::PARAM_INT);
                        $stmt->bindParam(':pickup', $pickup);
                        $stmt->bindParam(':return', $return);
                        $stmt->bindParam(':payment', $total_payment);

                        $stmt->execute();
                        $conn->commit();

                        // Redirect to the payment page
                        header("Location: payment.php?car_id=$car_id&total_payment=$total_payment");
                        exit();
                    } else {
                        $conn->rollBack();
                        $message = "Failed to reserve the car. It may no longer be available.";
                    }
                } catch (Exception $e) {
                    $conn->rollBack();
                    $message = "Error: " . $e->getMessage();
                }
            } else {
                $message = "Car not found.";
            }
        }
    }
}

// Fetch available cars
$query = "
    SELECT car.*, office.office_no, office.city, office.address
    FROM car
    JOIN office ON car.office_no = office.office_no
    WHERE car.status='Available'";
$stmt = $conn->prepare($query);
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
            background: url("background.png") no-repeat center center fixed;
            padding-top: 60px; /* To prevent content from being hidden under the fixed navbar */
        }
        nav {
            background-color: #022454;
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
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
            padding: 12px 20px;
            transition: background 0.3s;
        }

        nav ul li a:hover {
            background-color: #575757;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #022454;
            margin-bottom: 30px;
            padding: 10px;
            border: 3px solid #ffffff; /* Border color and thickness */
            background-color: #ffffff; /* Background color */
            border-radius: 10px; /* Optional: Rounded corners */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Shadow effect */
        }
        .car-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 10px;
        }
        .car-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #fff;
            padding: 15px;
            text-align: center;
        }
        .car-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .reserve-form input,
        .reserve-form button {
            margin: 5px 0;
        }
        .reserve-button {
            padding: 10px 20px;
            background-color: #022454;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .reserve-button:hover {
            background-color: #0056b3;
        }
        footer {
            background-color: #022454;
            padding: 7px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav>
        <ul>
            <li><a href="usermenu.html">Home</a></li>
            <li><a href="useraboutus.html">About</a></li>
            <li><a href="usercontactus.html">Contact Us</a></li>
            <li><a href="userlocation.html">Locations</a></li>
            <li><a href="reserve.php">Reserve</a></li>
            <li><a href="search.html">Search</a></li>
            <li><a href="menu.html">Log-Out</a></li>
        </ul>
    </nav>

    <!-- Title -->
    <h1>Available Cars for Rental</h1>

    <?php if (isset($message)): ?>
        <script>
            alert("<?php echo addslashes($message); ?>");
        </script>
    <?php endif; ?>

    <div class="car-list">
    <?php if (count($cars) > 0): ?>
        <?php foreach ($cars as $car): ?>
            <div class="car-card">
                <img src="<?php echo htmlspecialchars($car['photo']); ?>" alt="Car Image" class="car-image">
                <h2><?php echo htmlspecialchars($car['brand'] . " " . $car['model']); ?></h2>
                <p><strong>Plate:</strong> <?php echo htmlspecialchars($car['plate_id']); ?></p>
                <p><strong>Year:</strong> <?php echo htmlspecialchars($car['year']); ?></p>
                <p><strong>Color:</strong> <?php echo htmlspecialchars($car['color']); ?></p>
                <p><strong>Class:</strong> <?php echo htmlspecialchars($car['class']); ?></p>
                <p><strong>Daily Price:</strong> $<?php echo htmlspecialchars($car['daily_price']); ?></p>
                <p><strong>Office Location:</strong> <?php echo htmlspecialchars($car['city'] . ", " . $car['address']); ?></p>
                <form method="POST" class="reserve-form">
                    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                    <input type="hidden" name="ssn" placeholder="SSN" required>
                    <input type="date" name="pickup" placeholder="Pickup Date" required>
                    <input type="date" name="return" placeholder="Return Date" required>
                    <button type="submit" class="reserve-button">Reserve</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No available cars at the moment.</p>
    <?php endif; ?>
</div>
    <footer>
        &copy; 2024 Car Rental System. All Rights Reserved.
    </footer>
</body>
</html>
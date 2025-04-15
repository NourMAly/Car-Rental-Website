<?php
// Connect to the database
$mysqli = new mysqli('localhost', 'root', '', 'carrental');

// Check connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Collect form data from the GET request
$plate_id = isset($_GET['plate_id']) ? $_GET['plate_id'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$model = isset($_GET['model']) ? $_GET['model'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$color = isset($_GET['color']) ? $_GET['color'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$fname = isset($_GET['fname']) ? $_GET['fname'] : '';
$lname = isset($_GET['lname']) ? $_GET['lname'] : '';
$ssn = isset($_GET['ssn']) ? $_GET['ssn'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
$return = isset($_GET['return']) ? $_GET['return'] : '';
$payment = isset($_GET['payment']) ? $_GET['payment'] : '';

// Construct the base SQL query
$sql = "SELECT c.plate_id, c.brand, c.model, c.year, c.status, c.color, c.class, c.daily_price, c.office_no,
        cus.fname, cus.lname, cus.ssn, cus.gender, r.pickup, r.return, r.payment
        FROM car AS c
        LEFT JOIN rent AS r ON c.car_id = r.car_id
        LEFT JOIN customer AS cus ON r.ssn = cus.ssn
        WHERE 1";

// Add conditions to filter based on user input
if ($plate_id) {
    $sql .= " AND c.plate_id LIKE ?";
}
if ($brand) {
    $sql .= " AND c.brand LIKE ?";
}
if ($model) {
    $sql .= " AND c.model LIKE ?";
}
if ($year) {
    $sql .= " AND c.year = ?";
}
if ($color) {
    $sql .= " AND c.color LIKE ?";
}
if ($class) {
    $sql .= " AND c.class LIKE ?";
}
if ($fname) {
    $sql .= " AND cus.fname LIKE ?";
}
if ($lname) {
    $sql .= " AND cus.lname LIKE ?";
}
if ($ssn) {
    $sql .= " AND cus.ssn = ?";
}
if ($gender) {
    $sql .= " AND cus.gender = ?";
}
if ($pickup) {
    $sql .= " AND r.pickup = ?";
}
if ($return) {
    $sql .= " AND r.return = ?";
}
if ($payment) {
    $sql .= " AND r.payment = ?";
}

// Prepare the statement
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $params = [];
    $types = '';

    if ($plate_id) {
        $params[] = "%" . $plate_id . "%";
        $types .= 's';
    }
    if ($brand) {
        $params[] = "%" . $brand . "%";
        $types .= 's';
    }
    if ($model) {
        $params[] = "%" . $model . "%";
        $types .= 's';
    }
    if ($year) {
        $params[] = $year;
        $types .= 'i';
    }
    if ($color) {
        $params[] = "%" . $color . "%";
        $types .= 's';
    }
    if ($class) {
        $params[] = "%" . $class . "%";
        $types .= 's';
    }
    if ($fname) {
        $params[] = "%" . $fname . "%";
        $types .= 's';
    }
    if ($lname) {
        $params[] = "%" . $lname . "%";
        $types .= 's';
    }
    if ($ssn) {
        $params[] = $ssn;
        $types .= 's';
    }
    if ($gender) {
        $params[] = $gender;
        $types .= 's';
    }
    if ($pickup) {
        $params[] = $pickup;
        $types .= 's';
    }
    if ($return) {
        $params[] = $return;
        $types .= 's';
    }
    if ($payment) {
        $params[] = $payment;
        $types .= 'd';
    }

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Plate ID</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Color</th>
                        <th>Class</th>
                        <th>Daily Price</th>
                        <th>Office No</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>SSN</th>
                        <th>Gender</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Payment Amount</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['plate_id']) . '</td>
                    <td>' . htmlspecialchars($row['brand']) . '</td>
                    <td>' . htmlspecialchars($row['model']) . '</td>
                    <td>' . htmlspecialchars($row['year']) . '</td>
                    <td>' . htmlspecialchars($row['status']) . '</td>
                    <td>' . htmlspecialchars($row['color']) . '</td>
                    <td>' . htmlspecialchars($row['class']) . '</td>
                    <td>$' . htmlspecialchars($row['daily_price']) . '</td>
                    <td>' . htmlspecialchars($row['office_no']) . '</td>
                    <td>' . htmlspecialchars($row['fname']) . '</td>
                    <td>' . htmlspecialchars($row['lname']) . '</td>
                    <td>' . htmlspecialchars($row['ssn']) . '</td>
                    <td>' . htmlspecialchars($row['gender']) . '</td>
                    <td>' . htmlspecialchars($row['pickup']) . '</td>
                    <td>' . htmlspecialchars($row['return']) . '</td>
                    <td>$' . htmlspecialchars($row['payment']) . '</td>
                </tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p class="no-results-message">No results found.</p>';
    }

    $stmt->close();
} else {
    echo "Error preparing query: " . $mysqli->error;
}

$mysqli->close();
?>

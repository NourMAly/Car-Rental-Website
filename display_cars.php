<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Cars</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }

        table th {
            background-color: #022454;
            color: white;
        }

        table img {
            width: 50px;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>Available Cars</h1>
    <table>
        <thead>
            <tr>
                <th>Plate ID</th>
                <th>Model</th>
                <th>Year</th>
                <th>Color</th>
                <th>Brand</th>
                <th>Class</th>
                <th>Image</th>
                <th>Price/Day</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conn = new mysqli('localhost', 'root', '', 'carrental');
            if ($conn->connect_error) {
                die('Connection failed: ' . $conn->connect_error);
            }

            $sql = "SELECT plate_id, model, year, color, brand, class, photo, daily_price, status FROM car";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['plate_id']}</td>
                            <td>{$row['model']}</td>
                            <td>{$row['year']}</td>
                            <td>{$row['color']}</td>
                            <td>{$row['brand']}</td>
                            <td>{$row['class']}</td>
                            <td><img src='{$row['photo']}' alt='Car Image'></td>
                            <td>\${$row['daily_price']}</td>
                            <td>{$row['status']}</td>
                            </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No cars found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
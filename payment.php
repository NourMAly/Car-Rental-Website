<?php
// Fetch car and total payment from the URL
if (isset($_GET['car_id'], $_GET['total_payment'])) {
    $car_id = $_GET['car_id'];
    $total_payment = $_GET['total_payment'];
} else {
    // If no data is passed, redirect back to the home page
    header("Location: reserve.php");
    exit();
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_number'], $_POST['expiry_date'], $_POST['cvv'])) {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // For demonstration purposes, assume the payment is successful
    // Normally, you would process the card details using a payment gateway API

    // Simulate payment success
    $payment_successful = true; // You would replace this with real payment gateway logic

    if ($payment_successful) {
        $message = "Payment successful! You will be redirected shortly.";
        $redirect_url = "usermenu.html"; // Redirect to homepage after successful payment
    } else {
        $message = "Payment failed. Please try again.";
        $redirect_url = "payment.php"; // Stay on the payment page in case of failure
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental - Payment</title>
    <style>
        /* Add your styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #022454;
            margin-bottom: 30px;
        }
        .payment-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .payment-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .payment-form button {
            width: 100%;
            padding: 10px;
            background-color: #022454;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .payment-form button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            text-align: center;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .popup button {
            margin-top: 20px;
            padding: 10px;
            background-color: #022454;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup button:hover {
            background-color: #022454;
        }
    </style>
</head>
<body>
    <h1>Payment for Reservation</h1>

    <?php if (isset($message)): ?>
        <script>
            // Show the popup with the message
            var message = "<?php echo addslashes($message); ?>";
            var redirectUrl = "<?php echo $redirect_url; ?>";

            function showPopup() {
                var popup = document.getElementById('payment-popup');
                var popupMessage = document.getElementById('popup-message');
                popupMessage.textContent = message;
                popup.style.display = 'block';

                // Set a timer to redirect after a few seconds
                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 3000); // Redirect after 3 seconds
            }

            window.onload = showPopup;
        </script>
    <?php endif; ?>

    <div class="payment-form">
        <form method="POST">
            <label for="card_number">Card Number</label>
            <input type="text" name="card_number" id="card_number" placeholder="Enter your card number" required>

            <label for="expiry_date">Expiry Date (MM/YY)</label>
            <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" required>

            <label for="cvv">CVV</label>
            <input type="text" name="cvv" id="cvv" placeholder="Enter your CVV" required>

            <button type="submit">Pay $<?php echo number_format($total_payment, 2); ?></button>
        </form>
    </div>

    <!-- Popup for Payment Success -->
    <div id="payment-popup" class="popup">
        <p id="popup-message"></p>
        <button onclick="window.location.href='<?php echo $redirect_url; ?>'">Close</button>
    </div>
</body>
</html>

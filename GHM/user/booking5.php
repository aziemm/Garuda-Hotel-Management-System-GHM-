<?php
// Initialize variables
$booking_id = null;
$total_price = null;

// Check if booking_id and total_price are provided via GET or POST
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['booking_id']) && isset($_GET['total_price'])) {
    $booking_id = $_GET['booking_id'];
    $total_price = $_GET['total_price'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id']) && isset($_POST['total_price'])) {
    $booking_id = $_POST['booking_id'];
    $total_price = $_POST['total_price'];
} else {
    echo "Booking ID or Total Price not provided.";
    exit;
}

// Database connection details
$host = 'localhost';
$port = '1521';
$dbname = 'xe';
$username = 'GHM';
$password = 'GHM123';

// Connect to Oracle database
$conn = oci_connect($username, $password, "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA =(SID = $dbname)))");

if (!$conn) {
    $e = oci_error();
    echo "Sorry, connection failed: " . $e['message'];
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if addons are selected
    if (isset($_POST['addons']) && !empty($_POST['addons'])) {
        // Retrieve selected addons
        $addons = $_POST['addons'];

        // Check if addons have already been added
        $sql_check_addons = "SELECT COUNT(*) AS addon_count FROM BOOKING_ADDON WHERE BookingID = :booking_id";
        $stmt_check_addons = oci_parse($conn, $sql_check_addons);
        oci_bind_by_name($stmt_check_addons, ':booking_id', $booking_id);
        oci_execute($stmt_check_addons);
        $row = oci_fetch_assoc($stmt_check_addons);
        $addon_count = $row['ADDON_COUNT'];
        oci_free_statement($stmt_check_addons);

        if ($addon_count > 0) {
            echo "Add-ons have already been added.";
        } else {
            // Insert selected addons into BOOKING_ADDON table
            foreach ($addons as $addon_id) {
                $sql_addon = "INSERT INTO BOOKING_ADDON (BookingID, AddOnID) VALUES (:booking_id, :addon_id)";
                $stmt_addon = oci_parse($conn, $sql_addon);
                oci_bind_by_name($stmt_addon, ':booking_id', $booking_id);
                oci_bind_by_name($stmt_addon, ':addon_id', $addon_id);
                
                $result_addon = oci_execute($stmt_addon);
                
                if (!$result_addon) {
                    $e = oci_error($stmt_addon);
                    echo "Error occurred while adding addon: " . $e['message'];
                    oci_free_statement($stmt_addon);
                    oci_close($conn);
                    exit;
                }
                
                oci_free_statement($stmt_addon);
            }

            // Update final total price in BOOKING table
            $sql_booking = "UPDATE BOOKING SET TotalPrice = :total_price WHERE BookingID = :booking_id";
            $stmt_booking = oci_parse($conn, $sql_booking);
            oci_bind_by_name($stmt_booking, ':total_price', $total_price);
            oci_bind_by_name($stmt_booking, ':booking_id', $booking_id);

            $result_booking = oci_execute($stmt_booking);

            if ($result_booking) {
                // Redirect to sixthpage after successful update
                header("Location: booking6.php?booking_id=$booking_id&total_price=$total_price");
                exit;
            } else {
                $e = oci_error($stmt_booking);
                echo "Error occurred while updating total price: " . $e['message'];
            }

            oci_free_statement($stmt_booking);
        }
    } else {
        echo "No add-ons selected.";
    }
}

// Example usage: Form to select add-ons
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Add-Ons</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: left; /* Adjusted to align content to the left */
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-image: url('image/imgbooking5.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-size: calc(1.2 * 0.9);
        }

        h1 {
            color: #333333;
            margin-bottom: 20px;
            text-align: center; /* Centered header text */
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align form items to the left */
        }

        div {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        input[type="checkbox"] {
            display: none;
        }

        label {
            font-size: 16px;
            color: #333;
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            margin-left: 5px; /* Added margin to separate checkbox and label */
        }

        label:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid #E0A500;
            border-radius: 4px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        input[type="checkbox"]:checked + label:before {
            background-color: #E0A500;
            border-color: #E0A500;
        }

        input[type="checkbox"]:checked + label:after {
            content: '\2713'; /* Checkmark symbol */
            font-size: 14px;
            color: #ffffff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        input[type="submit"] {
            padding: 12px 20px;
            background-color: #E0A500;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 10px; /* Added margin to separate from checkboxes */
        }

        input[type="submit"]:hover {
            background-color: #cc9400;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select Add-Ons</h1>
        
        <form action="booking5.php" method="post">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

            <div>
                <input type="checkbox" id="addon1" name="addons[]" value="20">
                <label for="addon1">Package Name: Beauty Spa - Price: $50</label>
            </div>
            <div>
                <input type="checkbox" id="addon2" name="addons[]" value="21">
                <label for="addon2">Package Name: Water ThemePark - Price: $70</label>
            </div>
            <div>
                <input type="checkbox" id="addon3" name="addons[]" value="22">
                <label for="addon3">Package Name: Child Babysitting - Price: $28</label>
            </div>
            <div>
                <input type="checkbox" id="addon4" name="addons[]" value="23">
                <label for="addon4">Package Name: Extra Bed - Price: $63</label>
            </div>
            <div>
                <input type="checkbox" id="addon5" name="addons[]" value="24">
                <label for="addon5">Package Name: Clothes Laundry - Price: $25</label>
            </div>
            <br>
            <input type="submit" value="Add Selected Add-Ons">
        </form>
    </div>
</body>
</html>



<?php

// Close the connection
oci_close($conn);
?>

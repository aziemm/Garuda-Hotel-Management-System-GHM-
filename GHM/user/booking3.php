<?php
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

// Check if room_id and room_type_id are set via POST parameters
if (isset($_POST['room_id']) && isset($_POST['room_type_id'])) {
    $room_id = $_POST['room_id'];
    $room_type_id = $_POST['room_type_id'];
} else {
    echo "Error: Room ID or Room Type ID not provided.";
    exit;
}

// Room prices per night
$room_prices = [
    500 => 500,  // EXECUTIVE ROOM
    501 => 350,  // DELUXE ROOM
    502 => 232   // STANDARD ROOM
];

// Initialize variables to store booking_id and total_price
$booking_id = null;
$total_price = null;

// Check if form data is set and handle the submission
if (isset($_POST['checkin_date']) && isset($_POST['checkout_date']) && isset($_POST['num_adults']) && isset($_POST['num_children'])) {
    // Retrieve form data
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $num_adults = $_POST['num_adults'];
    $num_children = $_POST['num_children'];

    // Calculate the number of nights
    $checkin = new DateTime($checkin_date);
    $checkout = new DateTime($checkout_date);
    $interval = $checkin->diff($checkout);
    $num_nights = $interval->days;

    // Get the room price per night based on room_type_id
    if (array_key_exists($room_type_id, $room_prices)) {
        $price_per_night = $room_prices[$room_type_id];
        $total_price = $num_nights * $price_per_night;
    } else {
        echo "Invalid room type ID.";
        exit;
    }

    // Fetch the latest GuestID from GUEST table
    $fetch_guest_sql = "SELECT MAX(GuestID) AS max_guest_id FROM GUEST";
    $stmt_fetch_guest = oci_parse($conn, $fetch_guest_sql);
    oci_execute($stmt_fetch_guest);
    $guest_row = oci_fetch_assoc($stmt_fetch_guest);
    $guest_id = $guest_row['MAX_GUEST_ID'];

    if (!$guest_id) {
        echo "Error: No guest ID found in GUEST table.";
        exit;
    }

    // Prepare and execute the insert statement
    $insert_sql = "INSERT INTO BOOKING (BookingID, GuestID, PaymentID, RoomID, CheckinDate, CheckoutDate, NumAdults, NumChildren, TotalPrice)
                   VALUES (BOOKING_SEQ.NEXTVAL, :guest_id, :payment_id, :room_id, TO_DATE(:checkin_date, 'YYYY-MM-DD'), TO_DATE(:checkout_date, 'YYYY-MM-DD'), :num_adults, :num_children, :total_price)";
    $stmt_insert = oci_parse($conn, $insert_sql);

    oci_bind_by_name($stmt_insert, ':guest_id', $guest_id);
    oci_bind_by_name($stmt_insert, ':payment_id', $payment_id); // Assuming payment_id is set elsewhere
    oci_bind_by_name($stmt_insert, ':room_id', $room_id);
    oci_bind_by_name($stmt_insert, ':checkin_date', $checkin_date);
    oci_bind_by_name($stmt_insert, ':checkout_date', $checkout_date);
    oci_bind_by_name($stmt_insert, ':num_adults', $num_adults);
    oci_bind_by_name($stmt_insert, ':num_children', $num_children);
    oci_bind_by_name($stmt_insert, ':total_price', $total_price);

    $insert_result = oci_execute($stmt_insert);

    if ($insert_result) {
        // Retrieve the BookingID after successful insertion
        $sql_booking_id = "SELECT BOOKING_SEQ.CURRVAL AS BID FROM DUAL";
        $stmt_booking_id = oci_parse($conn, $sql_booking_id);
        oci_execute($stmt_booking_id);
        $row = oci_fetch_assoc($stmt_booking_id);
        $booking_id = $row['BID'];

        // Update GuestID for the inserted booking
        $update_sql = "UPDATE BOOKING SET GuestID = :guest_id WHERE BookingID = :booking_id";
        $stmt_update = oci_parse($conn, $update_sql);

        oci_bind_by_name($stmt_update, ':guest_id', $guest_id);
        oci_bind_by_name($stmt_update, ':booking_id', $booking_id);

        $update_result = oci_execute($stmt_update);

        if (!$update_result) {
            echo "Error updating GuestID.";
            exit;
        }

        oci_free_statement($stmt_update);
        oci_free_statement($stmt_booking_id);

    oci_free_statement($stmt_insert);
    oci_free_statement($stmt_fetch_guest);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Complete</title>
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
            padding: 36px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-image: url('imgbooking2.jpg');
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
            margin-bottom: 24px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-size: 21.6px;
            margin-bottom: 12px;
            display: block;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        input[type="submit"] {
            background-color: #E0A500;
            color: white;
            padding: 18px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 19.2px;
            transition: background-color 0.3s ease;
            margin-top: 24px;
        }

        input[type="submit"]:hover {
            background-color: #cc9400;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Booking Complete</h1>
        
        <form method="post" action="booking4.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
            
            <label>Would you like to add additional services?</label><br>
            <input type="radio" id="yes" name="addon_decision" value="yes">
            <label for="yes">Yes</label>
            <input type="radio" id="no" name="addon_decision" value="no" checked>
            <label for="no">No</label><br><br>
            
            <input type="submit" value="Continue">
        </form>
    </div>
</body>
</html>

<?php
        exit;
    } else {
        $e = oci_error($stmt);
        echo "Error occurred: " . $e['message'];
    }

    // Free the statement and close the connection
    oci_free_statement($stmt);
    oci_close($conn);
} else {
    // Display the form to get check-in, check-out dates, and number of adults and children
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
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
            max-width: 840px; /* Increased width by 40% */
            padding: 36px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333333;
            margin-bottom: 24px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-size: 21.6px;
            margin-bottom: 12px;
        }

        input, select {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="date"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        input[type="submit"] {
            background-color: #E0A500;
            color: white;
            padding: 18px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 19.2px;
            transition: background-color 0.3s ease;
            margin-top: 24px;
        }

        input[type="submit"]:hover {
            background-color: #cc9400;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Booking Form</h2>
        <form method="post" action="booking3.php">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
            <input type="hidden" name="room_type_id" value="<?php echo htmlspecialchars($room_type_id); ?>">

            <label for="checkin_date">Check-in Date:</label>
            <input type="date" id="checkin_date" name="checkin_date" required>

            <label for="checkout_date">Check-out Date:</label>
            <input type="date" id="checkout_date" name="checkout_date" required>

            <label for="num_adults">Number of Adults:</label>
            <input type="number" id="num_adults" name="num_adults" min="1" required>

            <label for="num_children">Number of Children:</label>
            <input type="number" id="num_children" name="num_children" min="0">

            <input type="submit" value="NEXT">
        </form>
    </div>
</body>

</html>




<?php
}
?>

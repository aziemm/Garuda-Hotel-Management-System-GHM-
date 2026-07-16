<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    die('Could not connect to Oracle: ' . htmlentities($e['message']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_booking'])) {
        // Confirm Booking
        $branch_id = $_POST['branch'];
        $room_type_id = $_POST['room_type'];
        $checkin_date = $_POST['checkin_date'];
        $checkout_date = $_POST['checkout_date'];
        $num_adults = $_POST['num_adults'];
        $num_children = $_POST['num_children'];
        $addon_ids = isset($_POST['addons']) ? $_POST['addons'] : [];
        $user_email = $_SESSION['user_email'];

        // Check for available rooms
        $get_room_query = "SELECT RoomID, NoRoom FROM ROOM WHERE RoomTypeID = :room_type_id AND BranchID = :branch_id AND Status = 'Available' ORDER BY dbms_random.value";
        $stid = oci_parse($conn, $get_room_query);
        oci_bind_by_name($stid, ':room_type_id', $room_type_id);
        oci_bind_by_name($stid, ':branch_id', $branch_id);
        oci_execute($stid);

        if (oci_fetch($stid)) {
            $room_id = oci_result($stid, 'ROOMID');
            $no_room = oci_result($stid, 'NOROOM');
            oci_free_statement($stid);

            // Proceed with the booking process
            // Start a transaction
            oci_execute(oci_parse($conn, "BEGIN NULL; END;"), OCI_NO_AUTO_COMMIT);

            // Get user GuestID
            $get_guest_query = "SELECT GuestID FROM GUEST WHERE Email = :email";
            $stid = oci_parse($conn, $get_guest_query);
            oci_bind_by_name($stid, ':email', $user_email);
            oci_execute($stid);
            if (oci_fetch($stid)) {
                $guest_id = oci_result($stid, 'GUESTID');
            } else {
                $_SESSION['error'] = 'Guest not found.';
                oci_close($conn);
                header('Location: homepage.php');
                exit;
            }
            oci_free_statement($stid);

            // Insert into PAYMENT table
            $payment_date = date('Y-m-d');
            $payment_status = 'Complete';
            $insert_payment_query = "INSERT INTO PAYMENT (PaymentID, PaymentDate, PaymentStatus) VALUES (PAYMENT_SEQ.NEXTVAL, TO_DATE(:payment_date, 'YYYY-MM-DD'), :payment_status) RETURNING PaymentID INTO :payment_id";
            $stid = oci_parse($conn, $insert_payment_query);
            oci_bind_by_name($stid, ':payment_date', $payment_date);
            oci_bind_by_name($stid, ':payment_status', $payment_status);
            oci_bind_by_name($stid, ':payment_id', $payment_id, 32);
            $result_payment = oci_execute($stid, OCI_NO_AUTO_COMMIT);
            oci_free_statement($stid);

            // Insert into BOOKING table
            $insert_booking_query = "INSERT INTO BOOKING (BookingID, GuestID, RoomID, PaymentID, CheckinDate, CheckoutDate, NumAdults, NumChildren, TotalPrice) VALUES (BOOKING_SEQ.NEXTVAL, :guest_id, :room_id, :payment_id, TO_DATE(:checkin_date, 'YYYY-MM-DD'), TO_DATE(:checkout_date, 'YYYY-MM-DD'), :num_adults, :num_children, :total_price)";
            $stid = oci_parse($conn, $insert_booking_query);
            oci_bind_by_name($stid, ':guest_id', $guest_id);
            oci_bind_by_name($stid, ':room_id', $room_id);
            oci_bind_by_name($stid, ':payment_id', $payment_id);
            oci_bind_by_name($stid, ':checkin_date', $checkin_date);
            oci_bind_by_name($stid, ':checkout_date', $checkout_date);
            oci_bind_by_name($stid, ':num_adults', $num_adults);
            oci_bind_by_name($stid, ':num_children', $num_children);

            // Calculate total price
            $date_diff_query = "SELECT (TO_DATE(:checkout_date, 'YYYY-MM-DD') - TO_DATE(:checkin_date, 'YYYY-MM-DD')) AS date_diff FROM DUAL";
            $stid_date_diff = oci_parse($conn, $date_diff_query);
            oci_bind_by_name($stid_date_diff, ':checkin_date', $checkin_date);
            oci_bind_by_name($stid_date_diff, ':checkout_date', $checkout_date);
            oci_execute($stid_date_diff);
            oci_fetch($stid_date_diff);
            $date_diff = oci_result($stid_date_diff, 'DATE_DIFF');
            oci_free_statement($stid_date_diff);

            $get_price_query = "SELECT PricePerNight FROM ROOM_TYPE WHERE RoomTypeID = :room_type_id";
            $stid_price = oci_parse($conn, $get_price_query);
            oci_bind_by_name($stid_price, ':room_type_id', $room_type_id);
            oci_execute($stid_price);
            oci_fetch($stid_price);
            $price_per_night = oci_result($stid_price, 'PRICEPERNIGHT');
            oci_free_statement($stid_price);

            $total_price = $date_diff * $price_per_night;

            // Add-on prices
            if (!empty($addon_ids)) {
                $addon_total_price = 0;
                foreach ($addon_ids as $addon_id) {
                    $get_addon_price_query = "SELECT Price FROM ADDON WHERE AddonID = :addon_id";
                    $stid_addon_price = oci_parse($conn, $get_addon_price_query);
                    oci_bind_by_name($stid_addon_price, ':addon_id', $addon_id);
                    oci_execute($stid_addon_price);
                    oci_fetch($stid_addon_price);
                    $addon_total_price += oci_result($stid_addon_price, 'PRICE');
                    oci_free_statement($stid_addon_price);
                }
                $total_price += $addon_total_price;
            }

            oci_bind_by_name($stid, ':total_price', $total_price);
            $result_booking = oci_execute($stid, OCI_NO_AUTO_COMMIT);
            oci_free_statement($stid);

            // Insert into BOOKING_ADDON table if any addons selected
            if (!empty($addon_ids)) {
                foreach ($addon_ids as $addon_id) {
                    $insert_booking_addon_query = "INSERT INTO BOOKING_ADDON (BookingID, AddonID) VALUES (BOOKING_SEQ.CURRVAL, :addon_id)";
                    $stid = oci_parse($conn, $insert_booking_addon_query);
                    oci_bind_by_name($stid, ':addon_id', $addon_id);
                    oci_execute($stid, OCI_NO_AUTO_COMMIT);
                    oci_free_statement($stid);
                }
            }

            if ($result_payment && $result_booking) {
                // Update room status to 'Occupied'
                $update_room_status_query = "UPDATE ROOM SET Status = 'Occupied' WHERE RoomID = :room_id";
                $stid = oci_parse($conn, $update_room_status_query);
                oci_bind_by_name($stid, ':room_id', $room_id);
                $result_update = oci_execute($stid, OCI_NO_AUTO_COMMIT);
                oci_free_statement($stid);

                if ($result_update) {
                    oci_commit($conn);
                    $_SESSION['message'] = 'Booking successful.';
                    echo "<script>
                    alert('Booking successful.');
                    window.location.href = 'homepage.php';
                    </script>";
                } else {
                    oci_rollback($conn);
                    $_SESSION['error'] = 'Error updating room status.';
                }
            } else {
                oci_rollback($conn);
                $_SESSION['error'] = 'Error processing booking.';
            }
        } else {
            oci_free_statement($stid);
            echo "<script>
            alert('The room is full.');
            window.location.href = 'booking.php';
            </script>";
        }
    }
    oci_close($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Room</title>
    <link rel="stylesheet" href="style/booking.css">
    <style>
        .home-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: rgba(76,68,182,0.808);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .home-button:hover {
            opacity: 0.82;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book a Room</h1>
        <a href="homepage.php" class="home-button">Back to Home</a>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="error-message">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="booking.php" method="POST">
            <div class="form-group">
                <label for="branch">Select Branch:</label>
                <select name="branch" id="branch" required>
                    <option value="">--Select Branch--</option>
                    <?php
                    $query = "SELECT BranchID, City, State FROM BRANCH";
                    $stid = oci_parse($conn, $query);
                    oci_execute($stid);
                    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                        echo "<option value=\"" . $row['BRANCHID'] . "\">" . $row['CITY'] . ", " . $row['STATE'] . "</option>";
                    }
                    oci_free_statement($stid);
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="room_type">Select Room Type:</label>
                <select name="room_type" id="room_type" required>
                    <option value="">--Select Room Type--</option>
                    <?php
                    $query = "SELECT RoomTypeID, Name FROM ROOM_TYPE";
                    $stid = oci_parse($conn, $query);
                    oci_execute($stid);
                    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                        echo "<option value=\"" . $row['ROOMTYPEID'] . "\">" . $row['NAME'] . "</option>";
                    }
                    oci_free_statement($stid);
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="checkin_date">Check-in Date:</label>
                <input type="date" id="checkin_date" name="checkin_date" required>
            </div>

            <div class="form-group">
                <label for="checkout_date">Check-out Date:</label>
                <input type="date" id="checkout_date" name="checkout_date" required>
            </div>

            <div class="form-group">
                <label for="num_adults">Number of Adults:</label>
                <input type="number" id="num_adults" name="num_adults" min="1" required>
            </div>

            <div class="form-group">
                <label for="num_children">Number of Children:</label>
                <input type="number" id="num_children" name="num_children" min="0" required>
            </div>

            <div class="form-group">
                <label for="addons">Select Add-ons:</label>
                <div id="addons">
                    <?php
                    $query = "SELECT AddonID, AddonName, Price FROM ADDON";
                    $stid = oci_parse($conn, $query);
                    oci_execute($stid);
                    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                        echo "<div><input type=\"checkbox\" name=\"addons[]\" value=\"" . $row['ADDONID'] . "\"> " . $row['ADDONNAME'] . " (RM " . $row['PRICE'] . ")</div>";
                    }
                    oci_free_statement($stid);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" name="confirm_booking">Confirm Booking</button>
            </div>
        </form>
    </div>
</body>
</html>





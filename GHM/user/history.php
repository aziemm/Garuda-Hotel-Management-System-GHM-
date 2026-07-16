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

$user_email = $_SESSION['user_email'];

$query = "
    SELECT 
        B.BookingID, B.CheckinDate, B.CheckoutDate, B.NumAdults, B.NumChildren, B.TotalPrice, 
        P.PaymentDate, P.PaymentStatus, 
        R.NoRoom, RT.Name AS RoomTypeName, RT.PricePerNight
    FROM 
        BOOKING B 
    JOIN 
        GUEST G ON B.GuestID = G.GuestID 
    JOIN 
        PAYMENT P ON B.PaymentID = P.PaymentID 
    JOIN 
        ROOM R ON B.RoomID = R.RoomID 
    JOIN 
        ROOM_TYPE RT ON R.RoomTypeID = RT.RoomTypeID 
    WHERE 
        G.Email = :email
    ORDER BY B.BookingID DESC";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ':email', $user_email);
oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking</title>
    <link rel="stylesheet" href="style/history.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* White with low opacity */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            colour: black;
        }

        .booking-box {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* White with low opacity */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .booking-box p {
            margin: 0 0 10px;
        }

        .booking-box .actions {
            margin-top: 10px;
            text-align: right;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: rgba(76, 68, 182, 0.808);;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-button:hover {
            opacity: 0.82;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Booking</h1>
        <?php if (oci_fetch_all($stid, $result, 0, -1, OCI_FETCHSTATEMENT_BY_ROW) > 0): ?>
            <?php foreach ($result as $row): ?>
                <div class="booking-box">
                    <p><strong>Booking ID:</strong> <?php echo htmlentities($row['BOOKINGID']); ?></p>
                    <p><strong>Check-in Date:</strong> <?php echo htmlentities($row['CHECKINDATE']); ?></p>
                    <p><strong>Check-out Date:</strong> <?php echo htmlentities($row['CHECKOUTDATE']); ?></p>
                    <p><strong>Number of Adults:</strong> <?php echo htmlentities($row['NUMADULTS']); ?></p>
                    <p><strong>Number of Children:</strong> <?php echo htmlentities($row['NUMCHILDREN']); ?></p>
                    <p><strong>Total Price: </strong>RM</> <?php echo htmlentities($row['TOTALPRICE']); ?></p>
                    <p><strong>Payment Date:</strong> <?php echo htmlentities($row['PAYMENTDATE']); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo htmlentities($row['PAYMENTSTATUS']); ?></p>
                    <p><strong>Room Number:</strong> <?php echo htmlentities($row['NOROOM']); ?></p>
                    <p><strong>Room Type:</strong> <?php echo htmlentities($row['ROOMTYPENAME']); ?></p>
                    <p><strong>Price Per Night: </strong>RM</> <?php echo htmlentities($row['PRICEPERNIGHT']); ?></p>
                    <div class="actions">
                        <form action="cancel_booking.php" method="post" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?php echo htmlentities($row['BOOKINGID']); ?>">
                            <input type="submit" value="Cancel Booking">
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>
        <a href="homepage.php" class="back-button">Back to Home</a>
    </div>
</body>
</html>

<?php
oci_free_statement($stid);
oci_close($conn);
?>



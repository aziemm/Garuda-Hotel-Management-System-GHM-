<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <style>
        body {
            background-image: url('adminHome.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 1200px;
            width: 90%;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn.return {
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            border: none;
            color: white;
            font-size: 16px;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            display: inline-block;
            border-radius: 4px;
            background-color: #FF0000;
        }
        .btn.return:hover {
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Booking Details</h1>
    <div class="action-buttons">
        <a href="dashboard.php" class="btn return">Return to Dashboard</a>
    </div>

    <table>
        <tr>
            <th>BookingID</th>
            <th>GuestID</th>
            <th>PaymentID</th>
            <th>RoomID</th>
            <th>CheckinDate</th>
            <th>CheckoutDate</th>
            <th>NumAdults</th>
            <th>NumChildren</th>
            <th>TotalPrice</th>
        </tr>
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            trigger_error('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            die('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES));
        }

        $query = 'SELECT * FROM BOOKING';
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error('Query parsing failed: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            die('Query parsing failed: ' . htmlentities($e['message'], ENT_QUOTES));
        }

        if (!oci_execute($stid)) {
            $e = oci_error($stid);
            trigger_error('Query execution failed: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            die('Query execution failed: ' . htmlentities($e['message'], ENT_QUOTES));
        }

        $bookings = array();
        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $bookings[] = $row;
        }

        if (!empty($bookings)) {
            foreach ($bookings as $booking_row) {
                echo '<tr>';
                foreach ($booking_row as $key => $value) {
                    echo '<td>' . htmlspecialchars($value ?? '') . '</td>';
                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="9">No data found</td></tr>';
        }

        oci_free_statement($stid);
        oci_close($conn);
        ?>

    </table>
</div>
</body>
</html>

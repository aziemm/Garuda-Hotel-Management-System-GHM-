<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $host = 'localhost';
    $port = '1521';
    $sid = 'xe';
    $username = 'GHM';
    $password = 'GHM123';

    $connectionString = "(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))
        (CONNECT_DATA =
            (SERVER = DEDICATED)
            (SERVICE_NAME = $sid)
        )
    )";

    try {
        $conn = oci_connect($username, $password, $connectionString);

        if (!$conn) {
            $e = oci_error();
            throw new Exception($e['message']);
        }

        // Get form data
        $paymentDate = $_POST['payment_date'];
        $paymentStatus = $_POST['payment_status'];
        $bookingID = $_POST['booking_id'];

        // Prepare and execute the SQL statement to insert the payment record with status "Processing"
        $sql = 'INSERT INTO PAYMENT (PaymentID, PaymentDate, PaymentStatus) VALUES (PAYMENT_SEQ.NEXTVAL, TO_DATE(:payment_date, \'YYYY-MM-DD\'), :payment_status) RETURNING PaymentID INTO :payment_id';
        $stid = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ':payment_date', $paymentDate);
        oci_bind_by_name($stid, ':payment_status', $paymentStatus);
        oci_bind_by_name($stid, ':payment_id', $paymentID, 32);

        $result = oci_execute($stid);

        if ($result) {
            // Update the PAYMENT record to set status to "Successful"
            $sql_update_payment = 'UPDATE PAYMENT SET PaymentStatus = \'Successful\' WHERE PaymentID = :payment_id';
            $stid_update_payment = oci_parse($conn, $sql_update_payment);
            oci_bind_by_name($stid_update_payment, ':payment_id', $paymentID);

            $result_update_payment = oci_execute($stid_update_payment);

            if ($result_update_payment) {
                // Update the BOOKING table with the generated PaymentID
                $sql_update_booking = 'UPDATE BOOKING SET PaymentID = :payment_id WHERE BookingID = :booking_id';
                $stid_update_booking = oci_parse($conn, $sql_update_booking);
                oci_bind_by_name($stid_update_booking, ':payment_id', $paymentID);
                oci_bind_by_name($stid_update_booking, ':booking_id', $bookingID);

                $result_update_booking = oci_execute($stid_update_booking);

                if ($result_update_booking) {
                    // Redirect to homepage.php after successful updates
                    oci_free_statement($stid_update_booking);
                    oci_free_statement($stid_update_payment);
                    oci_free_statement($stid);
                    oci_close($conn);
                    header('Location: homepage.php');
                    exit;
                } else {
                    $e = oci_error($stid_update_booking);
                    throw new Exception("Error updating booking: " . $e['message']);
                }

                oci_free_statement($stid_update_booking);
            } else {
                $e = oci_error($stid_update_payment);
                throw new Exception("Error updating payment status: " . $e['message']);
            }

            oci_free_statement($stid_update_payment);
        } else {
            $e = oci_error($stid);
            throw new Exception("Error inserting payment record: " . $e['message']);
        }

        // Free the statement identifier and close the connection
        oci_free_statement($stid);
        oci_close($conn);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .confirmation-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-image: url('image/imgbooking4.jpg');
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
            color: #4CAF50;
        }
        p {
            font-size: 18px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <h1>Payment Confirmation</h1>
        <p>Payment Date: <span id="paymentDate"></span></p>
        <p>Payment Status: <strong id="paymentStatus">Processing</strong></p>
        <form action="" method="post" id="paymentForm">
            <input type="hidden" name="payment_date" id="payment_date" value="">
            <input type="hidden" name="payment_status" id="payment_status" value="Processing">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($_GET['booking_id']); ?>">
            <button type="submit">Confirm</button>
        </form>
    </div>

    <script>
        // Function to format the current date
        function getFormattedDate() {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            const yyyy = today.getFullYear();
            return yyyy + '-' + mm + '-' + dd;
        }

        // Display the current date in the payment date span and set it in the form
        const paymentDate = getFormattedDate();
        document.getElementById('paymentDate').textContent = paymentDate;
        document.getElementById('payment_date').value = paymentDate;
    </script>
</body>
</html>

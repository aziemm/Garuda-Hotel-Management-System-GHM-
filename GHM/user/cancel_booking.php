<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];

    $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

    if (!$conn) {
        $e = oci_error();
        die('Could not connect to Oracle: ' . htmlentities($e['message']));
    }

    // Start a transaction
    oci_execute(oci_parse($conn, "BEGIN"), OCI_NO_AUTO_COMMIT);

    // Get the RoomID and PaymentID associated with the booking
    $get_details_query = "SELECT RoomID, PaymentID FROM BOOKING WHERE BookingID = :booking_id";
    $stid = oci_parse($conn, $get_details_query);
    oci_bind_by_name($stid, ':booking_id', $booking_id);
    oci_execute($stid);

    $room_id = null;
    $payment_id = null;
    if (oci_fetch($stid)) {
        $room_id = oci_result($stid, 'ROOMID');
        $payment_id = oci_result($stid, 'PAYMENTID');
    }

    oci_free_statement($stid);

    if ($room_id && $payment_id) {
        // Delete from BOOKING_ADDON table
        $delete_booking_addon_query = "DELETE FROM BOOKING_ADDON WHERE BookingID = :booking_id";
        $stid = oci_parse($conn, $delete_booking_addon_query);
        oci_bind_by_name($stid, ':booking_id', $booking_id);
        oci_execute($stid);

        // Delete from BOOKING table
        $delete_booking_query = "DELETE FROM BOOKING WHERE BookingID = :booking_id";
        $stid = oci_parse($conn, $delete_booking_query);
        oci_bind_by_name($stid, ':booking_id', $booking_id);
        $result_booking = oci_execute($stid, OCI_NO_AUTO_COMMIT);

        // Delete from PAYMENT table
        $delete_payment_query = "DELETE FROM PAYMENT WHERE PaymentID = :payment_id";
        $stid = oci_parse($conn, $delete_payment_query);
        oci_bind_by_name($stid, ':payment_id', $payment_id);
        $result_payment = oci_execute($stid, OCI_NO_AUTO_COMMIT);

        if ($result_booking && $result_payment) {
            // Update the ROOM status to 'Available'
            $update_room_status_query = "UPDATE ROOM SET Status = 'Available' WHERE RoomID = :room_id";
            $stid = oci_parse($conn, $update_room_status_query);
            oci_bind_by_name($stid, ':room_id', $room_id);
            $result_update = oci_execute($stid, OCI_NO_AUTO_COMMIT);

            if ($result_update) {
                // Commit the transaction if the delete and update were successful
                oci_commit($conn);
                $_SESSION['message'] = 'Booking cancelled successfully.';
            } else {
                // Rollback the transaction if the update failed
                oci_rollback($conn);
                $e = oci_error($stid);
                $_SESSION['error'] = 'Error updating room status: ' . htmlentities($e['message']);
            }
        } else {
            // Rollback the transaction if the delete failed
            oci_rollback($conn);
            $e = oci_error($stid);
            $_SESSION['error'] = 'Error cancelling booking or payment: ' . htmlentities($e['message']);
        }

        oci_free_statement($stid);
    } else {
        $_SESSION['error'] = 'Room ID or Payment ID not found for the booking.';
    }

    oci_close($conn);

    header('Location: history.php');

    exit;
}
?>


<?php
if (isset($_POST['addon_decision']) && isset($_POST['booking_id']) && isset($_POST['total_price'])) {
    $addon_decision = $_POST['addon_decision'];
    $booking_id = $_POST['booking_id'];
    $total_price = $_POST['total_price'];

    if ($addon_decision == 'yes') {
        header("Location: booking5.php?booking_id={$booking_id}&total_price={$total_price}");
    } else {
        header("Location: booking6.php?booking_id={$booking_id}&total_price={$total_price}");
    }
    exit;
} else {
    echo "Required parameters not provided.";
}
?>

<?php
// Create a connection to the Oracle database
$conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    echo "Connection failed: " . $e['message'];
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve new prices from the form
    $newPriceRoom1 = $_POST['priceRoom1'];
    $newPriceRoom2 = $_POST['priceRoom2'];
    $newPriceRoom3 = $_POST['priceRoom3'];

    // Update prices in the database
    $updateQuery1 = "UPDATE ROOM_TYPE SET PRICEPERNIGHT = :price WHERE ROOMTYPEID = 500";
    $updateQuery2 = "UPDATE ROOM_TYPE SET PRICEPERNIGHT = :price WHERE ROOMTYPEID = 501";
    $updateQuery3 = "UPDATE ROOM_TYPE SET PRICEPERNIGHT = :price WHERE ROOMTYPEID = 502";

    // Prepare statements
    $stmt1 = oci_parse($conn, $updateQuery1);
    $stmt2 = oci_parse($conn, $updateQuery2);
    $stmt3 = oci_parse($conn, $updateQuery3);

    // Bind parameters and execute queries
    oci_bind_by_name($stmt1, ':price', $newPriceRoom1);
    oci_bind_by_name($stmt2, ':price', $newPriceRoom2);
    oci_bind_by_name($stmt3, ':price', $newPriceRoom3);

    // Execute update statements and handle errors
    if (!oci_execute($stmt1)) {
        $e = oci_error($stmt1);
        echo "Error updating Room 1 price: " . $e['message'];
        exit;
    }
    
    if (!oci_execute($stmt2)) {
        $e = oci_error($stmt2);
        echo "Error updating Room 2 price: " . $e['message'];
        exit;
    }
    
    if (!oci_execute($stmt3)) {
        $e = oci_error($stmt3);
        echo "Error updating Room 3 price: " . $e['message'];
        exit;
    }

    // Optionally, provide feedback to the user that changes were saved
    echo "<p>Changes saved successfully!</p>";
}

// Function to retrieve current prices from database
function getCurrentPrice($conn, $roomTypeId) {
    $query = "SELECT PRICEPERNIGHT FROM ROOM_TYPE WHERE ROOMTYPEID = :roomTypeId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':roomTypeId', $roomTypeId);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    return $row ? $row['PRICEPERNIGHT'] : 'N/A';
}

// Debug: Dump $_POST to check form data submission
// var_dump($_POST);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <link rel="stylesheet" href="editStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('adminHome.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Libre Baskerville', serif;
            margin: 0;
            padding: 0;
            color: black; /* Set default text color */
        }

        .edit-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.9); /* Adjust background color opacity if needed */
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1 {
            color: #fff;
            margin-top: 0;
            font-weight: bold;
            color: black;
        }

        .return {
            margin-bottom: 20px;
        }

        .return .btn {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .return .btn:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .room {
            margin-bottom: 30px;
            text-align: center;
        }

        .room h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: black; /* Set room title text color */
        }

        .room label {
            display: block;
            margin-bottom: 10px;
            color: black; /* Set label text color */
        }

        .room input {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
            text-align: center;
        }

        .room .room-image {
            width: 400px; /* Increase width */
            height: 400px; /* Increase height */
            margin: 0 auto;
            border: 2px solid #ccc;
            border-radius: 10px;
            background-size: cover;
            background-position: center;
            margin-top: 10px;
        }

        .save button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #27ae60;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .save button:hover {
            background-color: #219d54;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h1>Edit Page</h1>
        <div class="return">
            <a href="dashboard.php" class="btn">Return</a>
        </div>
        <form action="edit.php" method="post">
            <div class="room">
                <h2>Room 1: Executive room</h2>
                <label>Current Price for Room 1: $<?php echo getCurrentPrice($conn, 500); ?></label>
                <label>New Price for Room 1: </label>
                <input type="number" name="priceRoom1" placeholder="New Price for Room 1">
                <div class="room-image" style="background-image: url('room1.jpg');"></div>
            </div>
            <div class="room">
                <h2>Room 2: Deluxe room</h2>
                <label>Current Price for Room 2: $<?php echo getCurrentPrice($conn, 501); ?></label>
                <label>New Price for Room 2: </label>
                <input type="number" name="priceRoom2" placeholder="New Price for Room 2">
                <div class="room-image" style="background-image: url('room2.jpg');"></div>
            </div>
            <div class="room">
                <h2>Room 3: Standard room</h2>
                <label>Current Price for Room 3: $<?php echo getCurrentPrice($conn, 502); ?></label>
                <label>New Price for Room 3: </label>
                <input type="number" name="priceRoom3" placeholder="New Price for Room 3">
                <div class="room-image" style="background-image: url('room3.jpg');"></div>
            </div>
            <div class="save">
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Database connection parameters
$db_username = 'GHM';
$db_password = 'GHM123';
$db_connection_string = 'localhost/XE';

// Establish the connection
$conn = oci_connect($db_username, $db_password, $db_connection_string);

if (!$conn) {
    $e = oci_error();
    echo "Error connecting to the database: " . $e['message'];
    exit;
}

$branch_id = null;

// Check if the branch_id is provided via GET parameter from secondpage.php
if (isset($_GET['branch_id'])) {
    $branch_id = $_GET['branch_id'];

    // Validate branch_id to prevent SQL injection
    if (!is_numeric($branch_id)) {
        echo "Invalid branch ID.";
        exit;
    }

    // Ensure branch_id is within valid range
    $branch_id = intval($branch_id);
    if ($branch_id !== 1 && $branch_id !== 2) {
        echo "Invalid branch ID.";
        exit;
    }
} else {
    echo "Branch ID not provided.";
    exit;
}

// Check if the form was submitted and the roomType is set
if (isset($_POST['roomType'])) {
    $room_type_id = $_POST['roomType'];

    // Check for available room with the selected room type and branch ID
    $sql_check_room = 'SELECT RoomID FROM ROOM WHERE RoomTypeID = :room_type_id AND BranchID = :branch_id AND Status = :status';
    $stid_check_room = oci_parse($conn, $sql_check_room);
    $status_available = 'Available';
    oci_bind_by_name($stid_check_room, ':room_type_id', $room_type_id);
    oci_bind_by_name($stid_check_room, ':branch_id', $branch_id);
    oci_bind_by_name($stid_check_room, ':status', $status_available);
    oci_execute($stid_check_room);
    $room = oci_fetch_assoc($stid_check_room);

    if ($room) {
        $room_id = $room['ROOMID'];

        // Update the room status to 'occupied'
        $sql_update_status = 'UPDATE ROOM SET Status = :status_occupied WHERE RoomID = :room_id';
        $stid_update_status = oci_parse($conn, $sql_update_status);
        $status_occupied = 'occupied';
        oci_bind_by_name($stid_update_status, ':status_occupied', $status_occupied);
        oci_bind_by_name($stid_update_status, ':room_id', $room_id);
        oci_execute($stid_update_status);

        // Redirect to booking3.php with the room_id and room_type_id
        echo "<form id='redirectForm' method='post' action='booking3.php'>
                <input type='hidden' name='room_id' value='$room_id'>
                <input type='hidden' name='room_type_id' value='$room_type_id'>
              </form>
              <script type='text/javascript'>
                  document.getElementById('redirectForm').submit();
              </script>";
        exit;
    } else {
        echo "No available rooms of the selected type in the chosen branch. Please select another room type.";
    }

    oci_free_statement($stid_check_room);
    oci_free_statement($stid_update_status);
} else {
    // Fetch room types for the form
    $sql = "SELECT RoomTypeID, Name FROM ROOM_TYPE";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Room Type</title>
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

        .container {
            max-width: 600px;
            padding: 36px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.1);
            text-align: center;
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
        }

        .select-menu {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .select-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 14px 24px;
            border: 1px solid #E0A500;
            border-radius: 6px;
            cursor: pointer;
        }

        .select-btn ion-icon {
            transition: transform 0.3s ease;
            color: #E0A500;
        }

        .list {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 0;
            overflow: hidden;
            background-color: #fff;
            border: 1px solid #E0A500;
            border-radius: 6px;
            transition: max-height 0.3s ease;
        }

        .option {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .option:hover {
            background-color: #f0f0f0;
        }

        .option ion-icon {
            margin-right: 12px;
            color: #E0A500;
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

        .select-menu.active .list {
            max-height: 360px;
        }

        .select-menu.active .select-btn ion-icon {
            transform: rotate(180deg);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Select Room Type</h1>
        <form action="booking2.php?branch_id=<?php echo $branch_id; ?>" method="post">
            <div class="select-menu" id="select-menu">
                <div class="select-btn" id="select-btn">
                    <span id="text">Choose a room type</span>
                    <ion-icon name="chevron-expand-outline" class="icon-arrow"></ion-icon>
                </div>

                <ul class="list" id="roomType-list">
                    <?php
                
                    while ($row = oci_fetch_assoc($stid)) {
                        echo "<li class='option' data-value='{$row['ROOMTYPEID']}' style='--i:5;'>
                                <ion-icon name='bed-outline'></ion-icon>
                                <span class='option-text'>{$row['NAME']}</span>
                              </li>";
                    }
                    ?>
                </ul>
                <input type="hidden" name="roomType" id="roomType" required>
            </div>
            <input type="submit" value="Continue">
        </form>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        document.getElementById('select-btn').addEventListener('click', function () {
            document.getElementById('select-menu').classList.toggle('active');
        });

        document.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function () {
                const value = this.getAttribute('data-value');
                const text = this.querySelector('.option-text').innerText;
                document.getElementById('roomType').value = value;
                document.getElementById('text').innerText = text;
                document.getElementById('select-menu').classList.remove('active');
            });
        });
    </script>
</body>

</html>

<?php
    oci_free_statement($stid);
}

oci_close($conn);
?>
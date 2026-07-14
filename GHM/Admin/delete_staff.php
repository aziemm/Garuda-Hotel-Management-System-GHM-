<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Staff</title>
    <style>
        body {
            background-image: url('adminHome.jpg'); /* Update with your image path */
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
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background for container */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            width: 90%;
            margin: auto;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .btn {
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            display: inline-block;
            border-radius: 4px;
        }
        .btn:hover {
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
        }
        .btn.cancel {
            background-color: #f44336;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Delete Staff</h1>

    <?php
    if (isset($_GET['staffid'])) {
        $staffid = $_GET['staffid'];

        // Connect to the Oracle database
        $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            trigger_error('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            die('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES));
        }

        // Prepare the SQL query
        $query = 'DELETE FROM STAFF WHERE STAFFID = :staffid';
        $stid = oci_parse($conn, $query);
        oci_bind_by_name($stid, ':staffid', $staffid);

        if (oci_execute($stid)) {
            echo '<p>Staff member successfully deleted.</p>';
        } else {
            $e = oci_error($stid);
            echo '<p>Failed to delete staff member: ' . htmlentities($e['message'], ENT_QUOTES) . '</p>';
        }

        echo '<a href="fetch_staff.php" class="btn">Back</a>';

        // Close the Oracle connection
        oci_free_statement($stid);
        oci_close($conn);
    } else {
        echo '<p>No staff ID provided.</p>';
        echo '<a href="fetch_staff.php" class="btn cancel">Back</a>';
    }
    ?>
</div>

</body>
</html>

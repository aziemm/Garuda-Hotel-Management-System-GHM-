<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Details</title>
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
        .return-btn {
            background-color: #FF0000;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }
        .return-btn:hover {
            background-color: #FF3333;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Guest Management</h1>
    <a href="dashboard.php" class="return-btn">Return to Dashboard</a>
    <table>
        <tr>
            <th>GuestID</th>
            <th>FirstName</th>
            <th>LastName</th>
            <th>DateOfBirth</th>
            <th>NoPhone</th>
            <th>Email</th>
        </tr>
        <?php
        // Enable error reporting for debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Connect to the Oracle database
        $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            trigger_error('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            die('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES));
        }

        // Prepare the SQL query
        $query = 'SELECT GUESTID, FIRSTNAME, LASTNAME, DATEOFBIRTH, NOPHONE, EMAIL FROM GUEST';
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

        // Fetch the data from the database
        $guests = array();
        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $guests[] = $row;
        }

        if (!empty($guests)) {
            foreach ($guests as $guest_row) {
                echo '<tr>';
                foreach ($guest_row as $key => $value) {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No data found</td></tr>';
        }

        // Close the Oracle connection
        oci_free_statement($stid);
        oci_close($conn);
        ?>
    </table>

</div>

</body>
</html>

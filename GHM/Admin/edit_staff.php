<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
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
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
    <h1>Edit Staff</h1>

    <div class="form-container">
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
            $query = 'SELECT * FROM STAFF WHERE STAFFID = :staffid';
            $stid = oci_parse($conn, $query);
            oci_bind_by_name($stid, ':staffid', $staffid);

            if (!oci_execute($stid)) {
                $e = oci_error($stid);
                trigger_error('Query execution failed: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                die('Query execution failed: ' . htmlentities($e['message'], ENT_QUOTES));
            }

            $staff = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

            if ($staff) {
                echo '<form action="process_staff.php" method="post">';
                echo '<input type="hidden" name="action" value="edit">';
                echo '<input type="hidden" name="id" value="' . htmlspecialchars($staff['STAFFID']) . '">';
                echo '<label for="branchid">Branch ID:</label>';
                echo '<input type="text" id="branchid" name="branchid" value="' . htmlspecialchars($staff['BRANCHID']) . '" required>';
                echo '<br>';
                echo '<label for="firstname">First Name:</label>';
                echo '<input type="text" id="firstname" name="firstname" value="' . htmlspecialchars($staff['FIRSTNAME']) . '" required>';
                echo '<br>';
                echo '<label for="lastname">Last Name:</label>';
                echo '<input type="text" id="lastname" name="lastname" value="' . htmlspecialchars($staff['LASTNAME']) . '" required>';
                echo '<br>';
                echo '<label for="position">Position:</label>';
                echo '<input type="text" id="position" name="position" value="' . htmlspecialchars($staff['POSITION']) . '" required>';
                echo '<br>';
                echo '<label for="dateofbirth">Date of Birth:</label>';
                echo '<input type="date" id="dateofbirth" name="dateofbirth" value="' . htmlspecialchars($staff['DATEOFBIRTH']) . '" required>';
                echo '<br>';
                echo '<label for="nophone">Phone Number:</label>';
                echo '<input type="text" id="nophone" name="nophone" value="' . htmlspecialchars($staff['NOPHONE']) . '" required>';
                echo '<br>';
                echo '<label for="email">Email:</label>';
                echo '<input type="email" id="email" name="email" value="' . htmlspecialchars($staff['EMAIL']) . '" required>';
                echo '<br>';
                echo '<label for="hiredate">Hire Date:</label>';
                echo '<input type="date" id="hiredate" name="hiredate" value="' . htmlspecialchars($staff['HIREDATE']) . '" required>';
                echo '<br>';
                echo '<button type="submit" class="btn">Save</button>';
                echo '<a href="fetch_staff.php" class="btn cancel">Cancel</a>';
                echo '</form>';
            } else {
                echo '<p>Staff member not found.</p>';
                echo '<a href="fetch_staff.php" class="btn cancel">Back</a>';
            }

            // Close the Oracle connection
            oci_free_statement($stid);
            oci_close($conn);
        } else {
            echo '<p>No staff ID provided.</p>';
            echo '<a href="fetch_staff.php" class="btn cancel">Back</a>';
        }
        ?>
    </div>
</div>

</body>
</html>

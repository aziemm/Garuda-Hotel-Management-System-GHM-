<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Connect to the Oracle database
    $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        trigger_error('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        die('Unable to connect to database: ' . htmlentities($e['message'], ENT_QUOTES));
    }

    switch ($action) {
        case 'add':
            $staffid = $_POST['staffid'];
            $branchid = $_POST['branchid'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $position = $_POST['position'];
            $dateofbirth = $_POST['dateofbirth'];
            $nophone = $_POST['nophone'];
            $email = $_POST['email'];
            $hiredate = $_POST['hiredate'];

            $query = 'INSERT INTO STAFF (StaffID, BranchID, FirstName, LastName, Position, DateOfBirth, NoPhone, Email, HireDate)
                      VALUES (:staffid, :branchid, :firstname, :lastname, :position, TO_DATE(:dateofbirth, \'YYYY-MM-DD\'), :nophone, :email, TO_DATE(:hiredate, \'YYYY-MM-DD\'))';
            $stid = oci_parse($conn, $query);
            oci_bind_by_name($stid, ':staffid', $staffid);
            oci_bind_by_name($stid, ':branchid', $branchid);
            oci_bind_by_name($stid, ':firstname', $firstname);
            oci_bind_by_name($stid, ':lastname', $lastname);
            oci_bind_by_name($stid, ':position', $position);
            oci_bind_by_name($stid, ':dateofbirth', $dateofbirth);
            oci_bind_by_name($stid, ':nophone', $nophone);
            oci_bind_by_name($stid, ':email', $email);
            oci_bind_by_name($stid, ':hiredate', $hiredate);

            if (oci_execute($stid)) {
                echo '<p>Staff member successfully added.</p>';
            } else {
                $e = oci_error($stid);
                trigger_error('Query execution failed: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                echo '<p>Failed to add staff member: ' . htmlentities($e['message'], ENT_QUOTES) . '</p>';
            }
            break;

        case 'edit':
            $staffid = $_POST['id'];
            $branchid = $_POST['branchid'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $position = $_POST['position'];
            $dateofbirth = $_POST['dateofbirth'];
            $nophone = $_POST['nophone'];
            $email = $_POST['email'];
            $hiredate = $_POST['hiredate'];

            $query = 'UPDATE STAFF
                      SET BranchID = :branchid, FirstName = :firstname, LastName = :lastname, Position = :position,
                          DateOfBirth = TO_DATE(:dateofbirth, \'YYYY-MM-DD\'), NoPhone = :nophone, Email = :email, HireDate = TO_DATE(:hiredate, \'YYYY-MM-DD\')
                      WHERE StaffID = :staffid';
            $stid = oci_parse($conn, $query);
            oci_bind_by_name($stid, ':staffid', $staffid);
            oci_bind_by_name($stid, ':branchid', $branchid);
            oci_bind_by_name($stid, ':firstname', $firstname);
            oci_bind_by_name($stid, ':lastname', $lastname);
            oci_bind_by_name($stid, ':position', $position);
            oci_bind_by_name($stid, ':dateofbirth', $dateofbirth);
            oci_bind_by_name($stid, ':nophone', $nophone);
            oci_bind_by_name($stid, ':email', $email);
            oci_bind_by_name($stid, ':hiredate', $hiredate);

            if (oci_execute($stid)) {
                echo '<p>Staff member successfully updated.</p>';
            } else {
                $e = oci_error($stid);
                trigger_error('Query execution failed: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                echo '<p>Failed to update staff member: ' . htmlentities($e['message'], ENT_QUOTES) . '</p>';
            }
            break;
    }

    echo '<a href="fetch_staff.php" class="btn">Back</a>';

    // Close the Oracle connection
    oci_free_statement($stid);
    oci_close($conn);
} else {
    echo '<p>Invalid request.</p>';
    echo '<a href="fetch_staff.php" class="btn cancel">Back</a>';
}
?>

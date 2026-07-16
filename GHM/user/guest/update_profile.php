<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['FirstName'];
    $lastname = $_POST['LastName'];
    $dateofbirth = $_POST['DateOfBirth'];
    $nophone = $_POST['NoPhone'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    // Convert the date of birth to the format expected by Oracle
    $dateofbirth_formatted = date('d-M-Y', strtotime($dateofbirth));

    // Connect to the Oracle database
    $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

    if (!$conn) {
        $e = oci_error();
        die('Could not connect to Oracle: ' . htmlentities($e['message']));
    }

    // Update the user data in the GUEST table
    $query = "UPDATE GUEST SET 
                FIRSTNAME = :firstname, 
                LASTNAME = :lastname, 
                DATEOFBIRTH = TO_DATE(:dateofbirth, 'DD-MON-YYYY'), 
                NOPHONE = :nophone, 
                EMAIL = :email, 
                PASSWORD = :password 
              WHERE EMAIL = :original_email";
    $stid = oci_parse($conn, $query);

    // Bind variables to the query
    oci_bind_by_name($stid, ':firstname', $firstname);
    oci_bind_by_name($stid, ':lastname', $lastname);
    oci_bind_by_name($stid, ':dateofbirth', $dateofbirth_formatted);
    oci_bind_by_name($stid, ':nophone', $nophone);
    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':password', $password);
    oci_bind_by_name($stid, ':original_email', $_SESSION['user_email']);

    // Execute the query
    if (oci_execute($stid)) {
        // Update the session email if it was changed
        $_SESSION['user_email'] = $email;
        echo "<script>
        alert('Update profile successful.');
        window.location.href = '/GHM/User/homepage.php';
      </script>";
    } else {
        $e = oci_error($stid);
        echo "Error updating profile: " . htmlentities($e['message']);
    }

    // Free the statement and close the connection
    oci_free_statement($stid);
    oci_close($conn);
}
?>


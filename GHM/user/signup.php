<?php
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

    // Insert the user data into the GUEST table
    $query = "INSERT INTO GUEST (FIRSTNAME, LASTNAME, DATEOFBIRTH, NOPHONE, EMAIL, PASSWORD) 
              VALUES (:firstname, :lastname, TO_DATE(:dateofbirth, 'DD-MON-YYYY'), :nophone, :email, :password)";
    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ':firstname', $firstname);
    oci_bind_by_name($stid, ':lastname', $lastname);
    oci_bind_by_name($stid, ':dateofbirth', $dateofbirth_formatted);
    oci_bind_by_name($stid, ':nophone', $nophone);
    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':password', $password);

    if (oci_execute($stid)) {
        echo "<script>
                alert('User registered successfully.');
                window.location.href = 'index.php';
              </script>";
    } else {
        $e = oci_error($stid);
        echo "Error: " . htmlentities($e['message']);
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/indexstyle2.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Sign Up</header>
            <form action="signup.php" method="post">
                <div class="field input">
                    <label for="FirstName">First Name</label>
                    <input type="text" name="FirstName" id="FirstName" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="LastName">Last Name</label>
                    <input type="text" name="LastName" id="LastName" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="DateOfBirth">Date Of Birth</label>
                    <input type="date" name="DateOfBirth" id="DateOfBirth" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="NoPhone">Phone Number</label>
                    <input type="text" name="NoPhone" id="NoPhone" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="Email">Email</label>
                    <input type="email" name="Email" id="Email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="Password">Password</label>
                    <input type="password" name="Password" id="Password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Sign Up">
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

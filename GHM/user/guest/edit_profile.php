<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    die("User not logged in.");
}

$user_email = $_SESSION['user_email'];

// Connect to the Oracle database
$conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    die('Could not connect to Oracle: ' . htmlentities($e['message']));
}

// Fetch the user data from the GUEST table
$query = "SELECT * FROM GUEST WHERE EMAIL = :email";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ':email', $user_email);
oci_execute($stid);

$user_data = oci_fetch_array($stid, OCI_ASSOC);

if (!$user_data) {
    die('User not found.');
}

oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/indexstyle2.css">
    <title>Edit Profile</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Edit Profile</header>
            <form action="../guest/update_profile.php" method="post">
                <div class="field input">
                    <label for="FirstName">First Name</label>
                    <input type="text" name="FirstName" id="FirstName" value="<?php echo htmlspecialchars($user_data['FIRSTNAME']); ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="LastName">Last Name</label>
                    <input type="text" name="LastName" id="LastName" value="<?php echo htmlspecialchars($user_data['LASTNAME']); ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="DateOfBirth">Date Of Birth</label>
                    <input type="date" name="DateOfBirth" id="DateOfBirth" value="<?php echo date('Y-m-d', strtotime($user_data['DATEOFBIRTH'])); ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="NoPhone">Phone Number</label>
                    <input type="text" name="NoPhone" id="NoPhone" value="<?php echo htmlspecialchars($user_data['NOPHONE']); ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="Email">Email</label>
                    <input type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($user_data['EMAIL']); ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="Password">Password</label>
                    <input type="password" name="Password" id="Password" value="<?php echo htmlspecialchars($user_data['PASSWORD']); ?>" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Update Profile">
                </div>
            </form>

            <!-- Back to Home button -->
            <a href="../homepage.php" class="back-button">Back to Home</a>
        </div>
    </div>
</body>
</html>


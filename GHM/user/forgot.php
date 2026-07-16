<?php
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $email = $_POST['EMAIL'];
    $new_password = $_POST['PASSWORD'];
    $confirm_password = $_POST['PASSWORD_CONFIRM'];

    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Connect to the Oracle database
        $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

        if (!$conn) {
            $e = oci_error();
            die('Could not connect to Oracle: ' . htmlentities($e['message']));
        }

        // Prepare and execute the update query
        $query = "UPDATE GUEST SET PASSWORD = :new_password WHERE EMAIL = :email";
        $stid = oci_parse($conn, $query);

        // Bind parameters
        oci_bind_by_name($stid, ':new_password', $new_password);
        oci_bind_by_name($stid, ':email', $email);

        if (oci_execute($stid)) {
            echo "<script>
                alert('Reset password successful.');
                window.location.href = 'index.php';
              </script>";
        } else {
            $e = oci_error($stid);
            $error_message = "Error updating password: " . htmlentities($e['message']);
        }

        oci_free_statement($stid);
        oci_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/indexstyle.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Reset Password</header>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="forgot.php" method="post">
                <div class="field input">
                    <label for="EMAIL">Email</label>
                    <input type="email" name="EMAIL" id="EMAIL" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="PASSWORD">New Password</label>
                    <input type="password" name="PASSWORD" id="PASSWORD" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="PASSWORD_CONFIRM">Confirm New Password</label>
                    <input type="password" name="PASSWORD_CONFIRM" id="PASSWORD_CONFIRM" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Reset Password">
                </div>
                <div class="links">
                    Already reset password? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

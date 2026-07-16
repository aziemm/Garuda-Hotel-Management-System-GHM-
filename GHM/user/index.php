index.php:

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['EMAIL'];
    $password = $_POST['PASSWORD'];

    $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');

    if (!$conn) {
        $e = oci_error();
        die('Could not connect to Oracle: ' . htmlentities($e['message']));
    }

    $query = "SELECT * FROM GUEST WHERE EMAIL = :email AND PASSWORD = :password";
    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':password', $password);

    oci_execute($stid);

    $row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

    if ($row) {
        $_SESSION['user_email'] = $row['EMAIL'];
        $_SESSION['guest_id'] = $row['GUESTID'];  // Store the guest ID in the session
        echo "<script>
                alert('Login successful.');
                window.location.href = 'homepage.php';
              </script>";
    } else {
        $error_message = "Wrong email or password.";
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
    <link rel="stylesheet" href="style/indexstyle.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Login</header>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <div class="field input">
                    <label for="EMAIL">Email</label>
                    <input type="email" name="EMAIL" id="EMAIL" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="PASSWORD">Password</label>
                    <input type="password" name="PASSWORD" id="PASSWORD" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login">
                </div>

                <div class="links">
                    Don't have an account? <a href="signup.php">Sign Up Now</a>
                </div>

                <div class="links">
                    <a href="forgot.php">Forgot Password</a>
                </div>
                <div class="links">
                    <a href="/GHM/Admin/index.php" class="button">Admin</a>
            </div>

            </form>
        </div>
    </div>
</body>
</html>

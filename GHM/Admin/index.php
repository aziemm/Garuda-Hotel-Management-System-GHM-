<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN PAGE</title>
    <link rel="stylesheet" href="indexStyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Playwrite+NG+Modern:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
     <form action="index.php" method="post">
        <h2>Admin login</h2>
        <p>
        <div class="links">
            <a href="/GHM/User/index.php" class="button">User Log in </a>
        </div>
        <div class="input-box">
            <label>StaffID</label>
            <input type="text" name="STAFFID" required>
            <i class='bx bxs-user'></i>
        </div>
        <div class="input-box">
            <label>Username</label>
            <input type="text" name="USERNAME" required>
            <i class='bx bxs-user'></i>
        </div>
        <div class="input-box">
            <label>Password</label>
            <input type="password" name="PASSWORD" required>
            <i class='bx bxs-lock-alt'></i>
        </div> <br>
        <input type="submit" name="LOGIN" class='btn' value="Login">
        </p>
     </form>
    </div>
</body>
</html>

<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $STAFFID = htmlspecialchars($_POST['STAFFID']);
        $USERNAME = htmlspecialchars($_POST['USERNAME']);
        $PASSWORD = htmlspecialchars($_POST['PASSWORD']);

        if (empty($STAFFID) || empty($USERNAME) || empty($PASSWORD)) {
            echo 'All fields are required';
        } else {
            $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');
            if (!$conn) {
                $e = oci_error();
                echo 'Could not connect: ' . htmlspecialchars($e['message']);
            } else {
                $sql = "SELECT STAFFID, USERNAME, PASSWORD FROM ADMIN WHERE STAFFID = :STAFFID AND USERNAME = :USERNAME";
                $stmt = oci_parse($conn, $sql);

                oci_bind_by_name($stmt, ':STAFFID', $STAFFID);
                oci_bind_by_name($stmt, ':USERNAME', $USERNAME);
                oci_execute($stmt);

                if ($row = oci_fetch_assoc($stmt)) {
                    if (password_verify($PASSWORD, $row['PASSWORD'])) {
                        echo "Login successful!";
                        header("Location: dashboard.php");
                        exit();
                        
                    } else {
                        echo '<p class="error-message">Invalid username or password</p>';
                    }
                } else {
                    echo '<p class="error-message">Invalid username or password</p>';
                }

                oci_free_statement($stmt);
                oci_close($conn);
            }
        }
    }
?>
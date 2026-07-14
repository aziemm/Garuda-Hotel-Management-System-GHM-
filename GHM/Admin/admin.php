
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN REGISTER</title>
    <link rel="stylesheet" href="adminStyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Playwrite+NG+Modern:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
     <form action="admin.php" method="post">
        <h2>Admin Register</h2>
        <p>
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
            <input type="submit" name="REGISTER" class='btn' value="REGISTER">
            <a href="dashboard.php" class="btn">RETURN</a>
        </p>
     </form>
    </div>
</body>
</html>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $STAFFID = $_POST["STAFFID"]; 
    $USERNAME = $_POST["USERNAME"]; 
    $PASSWORD = password_hash($_POST["PASSWORD"], PASSWORD_DEFAULT); 

  
    if (empty($STAFFID) || empty($USERNAME) || empty($PASSWORD)) {
        echo "All fields are required";
    } else {
    
        $conn = oci_connect('GHM', 'GHM123', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            echo 'Could not connect: ' . htmlspecialchars($e['message']);
        } else {
          
            $sql = "INSERT INTO ADMIN (STAFFID, USERNAME, PASSWORD) VALUES (:STAFFID, :USERNAME, :PASSWORD)";
            $stmt = oci_parse($conn, $sql);

           
            oci_bind_by_name($stmt, ":STAFFID", $STAFFID);
            oci_bind_by_name($stmt, ":USERNAME", $USERNAME);
            oci_bind_by_name($stmt, ":PASSWORD", $PASSWORD);

          
            $result = oci_execute($stmt);
            if (!$result) {
                echo "Could not register";
            } else {
                echo "Registration successful";
                header("Location: dashboard.php"); 
            }
            oci_free_statement($stmt);
            oci_close($conn);
        }
    }
}
?>

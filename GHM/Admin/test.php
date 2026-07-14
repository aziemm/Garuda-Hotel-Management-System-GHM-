<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Oracle Connect</title>
</head>
<body>
  <?php
    $user = "hr";
    $pass = "system";
    $host = "localhost/xe";
    $dbconnect = oci_connect ($user, $pass, $host);
    if($dbconnect){
      echo "XAMPP is successfully connected to Oracle Database </br>"; 
      echo "Hello Oracle Database";
    }else{
      echo "Database Not Connected";
      
    }
  ?>
</body>
</html>
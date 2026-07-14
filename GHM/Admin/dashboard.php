<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link rel="stylesheet" href="dashboardStyle.css">
</head>
<body>
    <div class="container">
        <div class="banner">
            <div class="navbar">
                <h1>Dashboard</h1>
                <ul>
                    <li><a href="admin.php">Admin</a></li>
                    <li><a href="fetch_staff.php">Staff</a></li>
                    <li><a href="fetch_booking.php">Bookings</a></li>
                    <li><a href="fetch_guests.php">Customers</a></li>
                    <li><a href="edit.php">Edit</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
            <div class="Welcome" id="welcomeMessage">
                <h1>Welcome Admin</h1>
            </div>
        </div>
    </div>

    <footer>
        <p>2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <script>
        window.onload = function() {
            document.getElementById('welcomeMessage').style.display = 'block';
        };
    </script>
</body>
</html>

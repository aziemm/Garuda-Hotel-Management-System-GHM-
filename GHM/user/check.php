<?php
// Database connection details
$username = 'GHM';
$password = 'GHM123';
$hostname = 'localhost';
$port = 1521;
$sid = 'XE';

// Connect to the Oracle database
$conn = oci_connect($username, $password, "//$hostname:$port/$sid");

if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
    exit;
}

// Fetch branches
$branchQuery = "SELECT BranchID, City FROM BRANCH ORDER BY City";
$branchStid = oci_parse($conn, $branchQuery);
oci_execute($branchStid);

$branches = [];
while ($row = oci_fetch_array($branchStid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $branches[] = $row;
}

// Check if a branch has been selected
$selectedBranch = isset($_POST['branch']) ? $_POST['branch'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Availability</title>
    <link rel="stylesheet" href="style/checkstyle.css">
</head>
<body>
    <div class="container">
        <h1>Room Availability</h1>
        <form method="post" action="check.php">
            <label for="branch">Select Branch:</label>
            <select name="branch" id="branch" required>
                <option value="">--Select Branch--</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= htmlspecialchars($branch['BRANCHID']) ?>" <?= $selectedBranch == $branch['BRANCHID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($branch['CITY']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Check Availability</button>
        </form>

        <?php
        if ($selectedBranch) {
            // Query to get room availability for the selected branch
            $query = "
                SELECT 
                    rt.Name AS RoomType, 
                    COUNT(CASE WHEN r.Status = 'Available' THEN 1 END) AS AvailableRooms, 
                    COUNT(CASE WHEN r.Status = 'occupied' THEN 1 END) AS OccupiedRooms
                FROM 
                    ROOM r
                JOIN 
                    ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID
                WHERE 
                    r.BranchID = :branchID
                GROUP BY 
                    rt.Name
                ORDER BY 
                    rt.Name
            ";

            $stid = oci_parse($conn, $query);
            oci_bind_by_name($stid, ':branchID', $selectedBranch);
            oci_execute($stid);

            echo '<div class="rooms">';
            while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                echo '<div class="room-box">';
                echo '<p>Room Type: ' . htmlspecialchars($row['ROOMTYPE']) . '</p>';
                echo '<p>Available Rooms: ' . htmlspecialchars($row['AVAILABLEROOMS']) . '</p>';
                echo '<p>Occupied Rooms: ' . htmlspecialchars($row['OCCUPIEDROOMS']) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <button onclick="location.href='homepage.php'">Back to Home</button>
    </div>
</body>
</html>

<?php
if (isset($stid)) {
    oci_free_statement($stid);
}
oci_close($conn);
?>



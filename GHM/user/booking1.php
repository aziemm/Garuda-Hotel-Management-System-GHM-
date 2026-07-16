<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Branch</title>
    
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-image: url('image/imgbooking3.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-size: calc(1.2 * 0.9);
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: calc(48px * 0.9);
            border-radius: calc(12px * 0.9);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            max-width: calc(600px * 0.9);
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: calc(24px * 0.9);
            font-size: calc(1.2rem * 0.9);
        }

        .select-menu {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .select-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: calc(14px * 0.9) calc(24px * 0.9);
            border: calc(1px * 0.9) solid #E0A500;
            border-radius: calc(6px * 0.9);
            cursor: pointer;
        }

        .select-btn ion-icon {
            transition: transform 0.3s ease;
            color: #E0A500;
        }

        .list {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 0;
            overflow: hidden;
            background-color: #fff;
            border: calc(1px * 0.9) solid #E0A500;
            border-radius: calc(6px * 0.9);
            transition: max-height 0.3s ease;
        }

        .option {
            display: flex;
            align-items: center;
            padding: calc(14px * 0.9) calc(24px * 0.9);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .option:hover {
            background-color: #f0f0f0;
        }

        .option ion-icon {
            margin-right: calc(12px * 0.9);
            color: #E0A500;
        }

        input[type="submit"] {
            background-color: #E0A500;
            color: white;
            padding: calc(18px * 0.9) calc(24px * 0.9);
            border: none;
            border-radius: calc(6px * 0.9);
            cursor: pointer;
            font-size: calc(19.2px * 0.9);
            transition: background-color 0.3s ease;
            margin-top: calc(24px * 0.9);
        }

        input[type="submit"]:hover {
            background-color: #cc9400;
        }

        .select-menu.active .list {
            max-height: calc(360px * 0.9);
        }

        .select-menu.active .select-btn ion-icon {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select Branch to Book</h1>
        
        <?php
        // Database connection parameters
        $db_username = 'GHM';
        $db_password = 'GHM123';
        $db_connection_string = 'localhost/XE';

        // Establish the connection
        $conn = oci_connect($db_username, $db_password, $db_connection_string);

        if (!$conn) {
            $e = oci_error();
            echo "Error connecting to the database: " . $e['message'];
            exit;
        }

        // Check if the form was submitted and the branch is set
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['branch'])) {
                $branch_id = $_POST['branch'];

                // Validate branch_id to prevent SQL injection
                if (!is_numeric($branch_id)) {
                    echo "Invalid branch ID.";
                    exit;
                }

                // Ensure branch_id is within valid range
                $branch_id = intval($branch_id);
                if ($branch_id !== 1 && $branch_id !== 2) {
                    echo "Invalid branch ID.";
                    exit;
                }

                // Redirect to thirdpage.php with the selected branch ID
                header("Location: booking2.php?branch_id=" . urlencode($branch_id));
                exit;
            } else {
                echo "Branch not selected.";
            }
        }

        oci_close($conn);
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="select-menu" id="select-menu">
                <div class="select-btn" id="select-btn">
                    <span id="text">Choose a branch</span>
                    <ion-icon name="chevron-expand-outline" class="icon-arrow"></ion-icon>
                </div>

                <ul class="list" id="branch-list">
                    <li class="option" data-value="1">
                        <ion-icon name="bed-outline"></ion-icon>
                        <span class="option-text">SHAH ALAM, SELANGOR</span>
                    </li>
                    <li class="option" data-value="2">
                        <ion-icon name="bed-outline"></ion-icon>
                        <span class="option-text">KOTA BAHRU, KELANTAN</span>
                    </li>
                </ul>
                <input type="hidden" name="branch" id="branch" required>
            </div>
            <input type="submit" value="Next">
        </form>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        document.getElementById('select-btn').addEventListener('click', function () {
            document.getElementById('select-menu').classList.toggle('active');
        });

        document.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function () {
                const value = this.getAttribute('data-value');
                const text = this.querySelector('.option-text').innerText;
                document.getElementById('branch').value = value;
                document.getElementById('text').innerText = text;
                document.getElementById('select-menu').classList.remove('active');
            });
        });
    </script>
</body>
</html>

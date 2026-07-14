<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <style>
        body {
            background-image: url('adminHome.jpg'); /* Update with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background for container */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            width: 90%;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            display: inline-block;
            border-radius: 4px;
        }
        .btn:hover {
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
        }
        .btn.cancel {
            background-color: #f44336;
        }
    </style>
    <script>
        function showConfirmation() {
            alert("New staff member has been added!");
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Add New Staff</h1>

    <div class="form-container">
        <form action="process_staff.php" method="post" onsubmit="showConfirmation()">
            <input type="hidden" name="action" value="add">
            <label for="staffid">Staff ID:</label>
            <input type="text" id="staffid" name="staffid" required>
            <br>
            <label for="branchid">Branch ID:</label>
            <input type="text" id="branchid" name="branchid" required>
            <br>
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required>
            <br>
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required>
            <br>
            <label for="position">Position:</label>
            <input type="text" id="position" name="position" required>
            <br>
            <label for="dateofbirth">Date of Birth:</label>
            <input type="date" id="dateofbirth" name="dateofbirth" required>
            <br>
            <label for="nophone">Phone Number:</label>
            <input type="text" id="nophone" name="nophone" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="hiredate">Hire Date:</label>
            <input type="date" id="hiredate" name="hiredate" required>
            <br>
            <button type="submit" class="btn">Save</button>
            <a href="fetch_staff.php" class="btn cancel">Cancel</a> <!-- Updated link -->
        </form>
    </div>
</div>

</body>
</html>

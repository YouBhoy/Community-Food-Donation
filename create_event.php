<?php
// Database connection
$host = 'localhost';
$dbname   = 'connecthub';     
$username = 'root';     
$password = '';     

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title        = $_POST['title'];
    $description  = $_POST['description'];
    $event_date   = $_POST['event_date'];
    $event_time   = $_POST['event_time'];
    $location     = $_POST['location'];
    $creator_id   = $_POST['creator_id'];
    $organization = $_POST['organization'];
    $event_type   = $_POST['event_type'];
    $is_volunteer = isset($_POST['is_volunteer']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO events (
        title, description, event_date, event_time, location, creator_id,
        organization, event_type, is_volunteer
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssiisi", $title, $description, $event_date, $event_time, $location, $creator_id, $organization, $event_type, $is_volunteer);

    if ($stmt->execute()) {
        $success_message = "✅ Event created successfully!";
    } else {
        $error_message = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            margin: 8px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="checkbox"] {
            margin-right: 8px;
        }

        label {
            font-weight: 500;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create an Event</h2>

    <?php if (!empty($success_message)) echo "<p class='message success'>$success_message</p>"; ?>
    <?php if (!empty($error_message)) echo "<p class='message error'>$error_message</p>"; ?>

    <form method="POST" action="">
        <label>Event Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" rows="4"></textarea>

        <label>Date</label>
        <input type="date" name="event_date" required>

        <label>Time</label>
        <input type="time" name="event_time" required>

        <label>Location</label>
        <input type="text" name="location">

        <label>Creator ID</label>
        <input type="number" name="creator_id" required>

        <label>Organization</label>
        <input type="text" name="organization">

        <label>Event Type</label>
        <select name="event_type">
            <option value="Workshop">Workshop</option>
            <option value="Charity">Charity</option>
            <option value="Food Drive">Food Drive</option>
            <option value="Other">Other</option>
        </select>

        <label>
            <input type="checkbox" name="is_volunteer"> This is a volunteer event
        </label>

        <br><br>
        <input type="submit" class="submit-btn" value="Create Event">
    </form>
</div>

</body>
</html>

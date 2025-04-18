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
<html>
<head>
    <title>Create Event</title>
</head>
<body>
    <h2>Create an Event</h2>

    <?php if (!empty($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if (!empty($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

    <form method="POST" action="">
        <label>Event Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Date:</label><br>
        <input type="date" name="event_date" required><br><br>

        <label>Time:</label><br>
        <input type="time" name="event_time" required><br><br>

        <label>Location:</label><br>
        <input type="text" name="location"><br><br>

        <label>Creator ID:</label><br>
        <input type="number" name="creator_id" required><br><br>

        <label>Organization:</label><br>
        <input type="text" name="organization"><br><br>

        <label>Event Type:</label><br>
        <select name="event_type">
            <option value="Conference">Conference</option>
            <option value="Workshop">Workshop</option>
            <option value="Fundraiser">Fundraiser</option>
            <option value="Webinar">Webinar</option>
            <option value="Other">Other</option>
        </select><br><br>

        <label>
            <input type="checkbox" name="is_volunteer" value="1">
            Is this a volunteer event?
        </label><br><br>

        <input type="submit" value="Create Event">
    </form>
</body>
</html>
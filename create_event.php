<?php
session_start();
require_once __DIR__ . '/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];
    $creator_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO events (title, description, location, event_date, creator_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $location, $event_date, $creator_id);
    
    if ($stmt->execute()) {
        header("Location: home.php?event_created=1");
        exit();
    } else {
        $error = "Failed to create event: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Create New Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-container img {
            height: 50px;
        }
        
        .header-buttons button {
            margin-left: 10px;
            padding: 8px 15px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-title {
            text-align: center;
            color: #1d2129;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1d2129;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #dddfe2;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: #166fe5;
        }
        
        .error-message {
            color: #ff0000;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="images/logo.png" alt="Community Food Share">
            <p>Fighting Hunger Together</p>
        </div>
        
        <div class="header-buttons">
            <button onclick="location.href='home.php'">Back to Home</button>
        </div>
    </header>
    
    <div class="form-container">
        <h1 class="form-title">Create New Food Event</h1>
        
        <?php if (isset($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form action="create_event.php" method="POST">
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" id="title" name="title" required placeholder="E.g., Community Food Drive">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required placeholder="Describe your event..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required placeholder="E.g., Main Campus Gym">
            </div>
            
            <div class="form-group">
                <label for="event_date">Date and Time</label>
                <input type="datetime-local" id="event_date" name="event_date" required>
            </div>
            
            <button type="submit" class="submit-btn">Create Event</button>
        </form>
    </div>
</body>
</html>
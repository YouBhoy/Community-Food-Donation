<?php
session_start();
require_once __DIR__ . '/db_connection.php';

$sql = "SELECT events.*, users.username 
        FROM events 
        JOIN users ON events.creator_id = users.id
        ORDER BY events.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Food Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
        
        nav {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        nav button {
            padding: 10px 20px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .events-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .event-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
        }
        
        .event-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0f2f5;
        }
        
        .event-card h2 {
            margin: 10px 0 5px;
            color: #1d2129;
        }
        
        .event-card .creator {
            color: #65676b;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .event-card .details {
            color: #1c1e21;
            margin-bottom: 15px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .interest-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .interest-buttons button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .interested-btn { 
            background: #1877f2; 
            color: white; 
        }
        
        .not-interested-btn { 
            background: #e4e6eb;
            color: #1c1e21;
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
            <button>👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?></button>
            <button>Logout</button>
        </div>
    </header>
    
    <nav>
        <button onclick="location.href='create_event.php'">Create Event</button>
        <button onclick="location.href='my_events.php'">My Events</button>
        <button onclick="location.href='search.php'">Search Events</button>
    </nav>
    
    <div class="events-container">
        <?php while($event = $result->fetch_assoc()): ?>
        <div class="event-card">
            <img src="images/event_placeholder.jpg" alt="Event Image">
            <h2><?= htmlspecialchars($event['title']) ?></h2>
            <p class="creator">Posted by: <?= htmlspecialchars($event['username']) ?></p>
            <p class="details">
                <?= nl2br(htmlspecialchars($event['description'])) ?>
            </p>
            
            <div class="interest-buttons">
                <button class="interested-btn" 
                        onclick="handleInterest(<?= $event['id'] ?>, 'interested')">
                    👍 Interested (<span id="int-count-<?= $event['id'] ?>">0</span>)
                </button>
                <button class="not-interested-btn" 
                        onclick="handleInterest(<?= $event['id'] ?>, 'not_interested')">
                    👎 Not Interested (<span id="not-count-<?= $event['id'] ?>">0</span>)
                </button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <script>
    function handleInterest(eventId, action) {
        fetch('handle_interest.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ event_id: eventId, action: action })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById(`${action}-count-${eventId}`).textContent = data.count;
        });
    }
    </script>
</body>
</html>
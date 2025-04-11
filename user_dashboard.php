<?php
// Database connection
$host = 'localhost';
$db = 'user_dashboard';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch user (assume ID = 1)
$stmt = $pdo->prepare("SELECT username, email, member_since FROM users WHERE id = 1");
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    $user['member'] = "Member since " . date("F Y", strtotime($user['member_since']));
} else {
    $user = [
        'username' => 'Unknown User',
        'email' => 'N/A',
        'member' => 'Unknown'
    ];
}

// Fetch events linked to user
$stmt = $pdo->prepare("
    SELECT e.name, e.date, e.location, ue.status
    FROM events e
    JOIN user_events ue ON e.id = ue.event_id
    WHERE ue.user_id = 1
");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f6f8;
        }

        .dashboard-header {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
        }

        .user-info h2 {
            margin: 0;
            font-size: 24px;
        }

        .user-info p {
            margin: 2px 0;
            color: gray;
        }

        .action-buttons {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .action-buttons button {
            background-color: darkblue;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin: 30px 0 10px 0;
        }

        .events {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .event-card, .settings-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .event-card h3 {
            margin-top: 0;
        }

        .status {
            font-weight: bold;
            color: green;
        }

        .view-details {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: darkblue;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .settings-card {
            margin-top: 30px;
        }

        .manage-settings {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: darkblue;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        footer {
            margin-top: 50px;
            padding: 40px;
            background-color: #ffffff;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            font-size: 14px;
            color: #555;
            border-radius: 10px;
        }

        footer div {
            flex: 1;
            min-width: 200px;
            margin-bottom: 20px;
        }

        footer strong {
            display: block;
            margin-bottom: 10px;
        }

        footer a {
            color: #555;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="dashboard-header">
    <div class="user-info">
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <p style="color: #888; font-style: italic;"><?php echo htmlspecialchars($user['member']); ?></p>
    </div>
</div>

<div class="action-buttons">
    <button>Events</button>
    <button>Organizations</button>
    <button>Donations</button>
    <button>Post</button>
    <button>Create Post</button>
    <button>Save</button>
</div>

<div class="section-title">Events</div>
<div class="events">
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($event['date'])); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                <p><strong>Status:</strong> <span class="status"><?php echo htmlspecialchars($event['status']); ?></span></p>
                <button class="view-details">View Details</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: gray; font-style: italic;">No events found.</p>
    <?php endif; ?>
</div>

<div class="settings-card">
    <h3>Account Settings</h3>
    <p>Manage your profile, preferences, and notifications.</p>
    <button class="manage-settings">Manage Settings</button>
</div>

<footer>
    <div>
        <strong>Community ConnectHub</strong>
        <p>Fostering partnerships and collaboration among university-based organizations.</p>
    </div>
    <div>
        <strong>Quick Links</strong>
        <p><a href="#">Home</a></p>
        <p><a href="#">About</a></p>
        <p><a href="#">Events</a></p>
        <p><a href="#">Organizations</a></p>
        <p><a href="#">News & Updates</a></p>
    </div>
    <div>
        <strong>Resources</strong>
        <p><a href="#">FAQ</a></p>
        <p><a href="#">Help Center</a></p>
        <p><a href="#">Privacy Policy</a></p>
        <p><a href="#">Terms of Service</a></p>
    </div>
    <div>
        <strong>Contact</strong>
        <p>contact@cconnecthub.edu</p>
        <p>University Campus</p>
    </div>
</footer>

</body>
</html>

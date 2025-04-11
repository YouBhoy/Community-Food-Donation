<?php
$user = [
    'username' => 'Carl Johnson',
    'email' => 'carljohnson@gmail.com',
    'member' => 'Member since September 2023'
];

$events = [
    [
        'name' => 'Tech Conference 2025',
        'date' => 'April 20, 2025',
        'location' => 'VMB 502',
        'status' => 'Registered'
    ],
    [
        'name' => 'Startup Meetup',
        'date' => 'May 15, 2025',
        'location' => 'GZB 101',
        'status' => 'Interested'
    ]
];
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
            color: black;
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
        <h2><?php echo $user['username']; ?></h2>
        <p><?php echo $user['email']; ?></p>
        <p style="color: #888; font-style: italic;"><?php echo $user['member']; ?></p>
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
    <?php foreach ($events as $event): ?>
        <div class="event-card">
            <h3><?php echo $event['name']; ?></h3>
            <p><strong>Date:</strong> <?php echo $event['date']; ?></p>
            <p><strong>Location:</strong> <?php echo $event['location']; ?></p>
            <p><strong>Status:</strong> <span class="status"><?php echo $event['status']; ?></span></p>
            <button class="view-details">View Details</button>
        </div>
    <?php endforeach; ?>
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

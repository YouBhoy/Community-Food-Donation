<?php
require_once 'db_connect.php';

// Redirect if not logged in
redirectIfNotLoggedIn();

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's events
$events_stmt = $pdo->prepare("
    SELECT e.*, i.action
    FROM events e
    LEFT JOIN interests i ON e.id = i.event_id AND i.user_id = ?
    WHERE i.user_id = ?
    ORDER BY e.event_date DESC
");
$events_stmt->execute([$user_id, $user_id]);
$events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's followed organizations
$orgs_stmt = $pdo->prepare("
    SELECT o.*
    FROM organizations o
    JOIN user_organization_follows f ON o.id = f.organization_id
    WHERE f.user_id = ?
");
$orgs_stmt->execute([$user_id]);
$organizations = $orgs_stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="org-avatar mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= substr($user['first_name'], 0, 1) ?>
                        </div>
                    </div>
                    <h5 class="card-title"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="badge bg-primary"><?= ucfirst(htmlspecialchars($user['role'])) ?></p>
                </div>
            </div>
            
            <div class="list-group mb-4">
                <a href="#" class="list-group-item list-group-item-action active">Dashboard</a>
                <a href="#" class="list-group-item list-group-item-action">My Events</a>
                <a href="#" class="list-group-item list-group-item-action">My Organizations</a>
                <a href="#" class="list-group-item list-group-item-action">Account Settings</a>
            </div>
        </div>
        
        <div class="col-md-9">
            <h2 class="mb-4">Dashboard</h2>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= count($events) ?></h5>
                            <p class="card-text">Events Interested</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= count($organizations) ?></h5>
                            <p class="card-text">Organizations Followed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <h5 class="card-title">0</h5>
                            <p class="card-text">Volunteer Hours</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Events</h5>
                    <a href="events.php" class="btn btn-sm btn-outline-primary">View All Events</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($events)): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events as $event): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($event['title']) ?></td>
                                            <td><?= date('M d, Y', strtotime($event['event_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $event['action'] == 'interested' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($event['action']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="event_details.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted my-4">You haven't shown interest in any events yet.</p>
                        <div class="text-center">
                            <a href="events.php" class="btn btn-primary">Explore Events</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Organizations I Follow</h5>
                    <a href="organizations.php" class="btn btn-sm btn-outline-primary">View All Organizations</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($organizations)): ?>
                        <div class="row">
                            <?php foreach ($organizations as $org): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="org-avatar me-2">
                                                    <?= substr($org['name'], 0, 1) ?>
                                                </div>
                                                <h6 class="mb-0"><?= htmlspecialchars($org['name']) ?></h6>
                                            </div>
                                            <p class="card-text small"><?= htmlspecialchars(substr($org['description'], 0, 80)) ?>...</p>
                                            <a href="view_organization.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted my-4">You haven't followed any organizations yet.</p>
                        <div class="text-center">
                            <a href="organizations.php" class="btn btn-primary">Discover Organizations</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

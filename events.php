<?php
require_once 'db_connect.php';

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming';
$event_type = isset($_GET['event_type']) ? $_GET['event_type'] : 'all';
$organization = isset($_GET['organization']) ? $_GET['organization'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_desc';

try {
    $sql = "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) AS username, 
        (SELECT COUNT(*) FROM interests WHERE event_id = e.id AND action = 'interested') AS interest_count,
        (SELECT COUNT(*) FROM interests WHERE event_id = e.id AND action = 'not_interested') AS not_interested_count,
        (SELECT action FROM interests WHERE event_id = e.id AND user_id = ?) AS user_action
        FROM events e
        LEFT JOIN users u ON e.creator_id = u.id";

    $where_clauses = [];
    $params = [];
    $types = "";

    if (isLoggedIn()) {
        $params[] = $_SESSION['user_id'];
    } else {
        $params[] = null;
    }

    if ($event_type != 'all') {
        $where_clauses[] = "e.event_type = ?";
        $params[] = $event_type;
    }

    if ($organization != 'all') {
        $where_clauses[] = "e.organization = ?";
        $params[] = $organization;
    }

    if (!empty($search)) {
        $where_clauses[] = "(e.title LIKE ? OR e.description LIKE ?)";
        $search_param = "%" . $search . "%";
        $params[] = $search_param;
        $params[] = $search_param;
    }

    if ($active_tab == 'upcoming') {
        $where_clauses[] = "e.event_date >= CURDATE()";
    } elseif ($active_tab == 'past') {
        $where_clauses[] = "e.event_date < CURDATE()";
    } elseif ($active_tab == 'volunteer') {
        $where_clauses[] = "e.is_volunteer = 1";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    switch ($sort_by) {
        case 'date_asc':
            $sql .= " ORDER BY e.event_date ASC";
            break;
        case 'popularity':
            $sql .= " ORDER BY interest_count DESC";
            break;
        case 'title':
            $sql .= " ORDER BY e.title ASC";
            break;
        case 'date_desc':
        default:
            $sql .= " ORDER BY e.event_date DESC";
            break;
    }

    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $i => $param) {
        $stmt->bindValue($i + 1, $param);
    }
    
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get event statistics
    $stats_sql = "SELECT 
                    COUNT(*) AS total_events,
                    COUNT(CASE WHEN event_date >= CURDATE() THEN 1 END) AS upcoming_events,
                    COUNT(CASE WHEN event_date < CURDATE() THEN 1 END) AS past_events,
                    COUNT(CASE WHEN is_volunteer = 1 THEN 1 END) AS volunteer_events
                  FROM events";
    $stats_stmt = $pdo->query($stats_sql);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

include 'header.php';
?>

<section class="hero">
    <div class="container">
        <h1>Events</h1>
        <p>Discover and participate in events happening across campus.</p>
    </div>
</section>

<div class="container">
    <?php if (isset($stats)): ?>
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-number"><?= $stats['total_events'] ?></div>
            <div class="stat-label">Total Events</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= $stats['upcoming_events'] ?></div>
            <div class="stat-label">Upcoming Events</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= $stats['past_events'] ?></div>
            <div class="stat-label">Past Events</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= $stats['volunteer_events'] ?></div>
            <div class="stat-label">Volunteer Opportunities</div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error_message) ?>
    </div>
    <?php endif; ?>
    
    <form method="GET" action="events.php" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="event-type" class="form-label">Event Type</label>
                <select id="event-type" name="event_type" class="form-select">
                    <option value="all" <?= $event_type == 'all' ? 'selected' : '' ?>>All Types</option>
                    <option value="workshop" <?= $event_type == 'workshop' ? 'selected' : '' ?>>Workshop</option>
                    <option value="charity" <?= $event_type == 'charity' ? 'selected' : '' ?>>Charity</option>
                    <option value="food-drive" <?= $event_type == 'food-drive' ? 'selected' : '' ?>>Food Drive</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="organization" class="form-label">Organization</label>
                <select id="organization" name="organization" class="form-select">
                    <option value="all" <?= $organization == 'all' ? 'selected' : '' ?>>All Organizations</option>
                    <option value="eco-club" <?= $organization == 'eco-club' ? 'selected' : '' ?>>Environmental Club</option>
                    <option value="charity-assoc" <?= $organization == 'charity-assoc' ? 'selected' : '' ?>>Athletics Association</option>
                    <option value="tech-society" <?= $organization == 'tech-society' ? 'selected' : '' ?>>Tech Innovation Society</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="sort-by" class="form-label">Sort By</label>
                <select id="sort-by" name="sort_by" class="form-select">
                    <option value="date_desc" <?= $sort_by == 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
                    <option value="date_asc" <?= $sort_by == 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
                    <option value="popularity" <?= $sort_by == 'popularity' ? 'selected' : '' ?>>Popularity</option>
                    <option value="title" <?= $sort_by == 'title' ? 'selected' : '' ?>>Title (A-Z)</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search events..." value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </div>
    </form>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a href="?tab=upcoming<?= !empty($event_type) && $event_type != 'all' ? '&event_type=' . urlencode($event_type) : '' ?><?= !empty($organization) && $organization != 'all' ? '&organization=' . urlencode($organization) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort_by) ? '&sort_by=' . urlencode($sort_by) : '' ?>" class="nav-link <?= $active_tab == 'upcoming' ? 'active' : '' ?>">Upcoming</a>
        </li>
        <li class="nav-item">
            <a href="?tab=past<?= !empty($event_type) && $event_type != 'all' ? '&event_type=' . urlencode($event_type) : '' ?><?= !empty($organization) && $organization != 'all' ? '&organization=' . urlencode($organization) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort_by) ? '&sort_by=' . urlencode($sort_by) : '' ?>" class="nav-link <?= $active_tab == 'past' ? 'active' : '' ?>">Past</a>
        </li>
        <li class="nav-item">
            <a href="?tab=volunteer<?= !empty($event_type) && $event_type != 'all' ? '&event_type=' . urlencode($event_type) : '' ?><?= !empty($organization) && $organization != 'all' ? '&organization=' . urlencode($organization) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort_by) ? '&sort_by=' . urlencode($sort_by) : '' ?>" class="nav-link <?= $active_tab == 'volunteer' ? 'active' : '' ?>">Volunteer</a>
        </li>
    </ul>
    
    <div class="events-grid">
        <?php 
        if (!empty($events)) {
            foreach($events as $event): 
                $category = $event['event_type'] ?? 'Workshop';
        ?>
        <div class="event-card">
            <div class="event-image">
                <img src="images/event_placeholder.jpg" alt="<?= htmlspecialchars($event['title']) ?>">
                <div class="event-category"><?= htmlspecialchars(ucfirst($category)) ?></div>
            </div>
            
            <div class="event-content">
                <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                <p class="event-org"><?= htmlspecialchars($event['username'] ?? 'Anonymous') ?></p>
                
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fas fa-calendar"></i>
                        <?= date('F j, Y', strtotime($event['event_date'] ?? date('Y-m-d'))) ?>
                    </div>
                    
                    <div class="event-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($event['location'] ?? 'Campus Center') ?>
                    </div>
                </div>
                
                <p class="event-description">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </p>
                
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-sm btn-outline-primary <?= $event['user_action'] == 'interested' ? 'active' : '' ?>" onclick="handleInterest(<?= $event['id'] ?>, 'interested')">
                        <i class="fas fa-thumbs-up"></i>
                        Interested (<?= $event['interest_count'] ?? 0 ?>)
                    </button>
                    <button class="btn btn-sm btn-outline-secondary <?= $event['user_action'] == 'not_interested' ? 'active' : '' ?>" onclick="handleInterest(<?= $event['id'] ?>, 'not_interested')">
                        <i class="fas fa-thumbs-down"></i>
                        Not Interested (<?= $event['not_interested_count'] ?? 0 ?>)
                    </button>
                </div>
                
                <a href="event_details.php?id=<?= $event['id'] ?>" class="btn btn-primary w-100">
                    View Details
                </a>
            </div>
        </div>
        <?php 
            endforeach; 
        } else {
        ?>
        <div class="col-12 text-center py-5">
            <div class="mb-3">
                <i class="fas fa-calendar-times fa-3x text-muted"></i>
            </div>
            <h4>No events found matching your criteria.</h4>
            <p class="text-muted">Try adjusting your filters or search terms.</p>
            <a href="events.php" class="btn btn-primary mt-2">View All Events</a>
        </div>
        <?php } ?>
    </div>
</div>

<script>
function handleInterest(eventId, action) {
    // Check if user is logged in
    <?php if (!isLoggedIn()): ?>
    window.location.href = 'login.php?redirect=events.php';
    return;
    <?php endif; ?>
    
    fetch('handle_interest.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<?php include 'footer.php'; ?>

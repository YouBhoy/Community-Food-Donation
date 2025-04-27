<?php
require_once 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit;
}

$event_id = $_GET['id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

try {
    $stmt = $pdo->prepare("CALL sp_get_event_details(?, ?)");
    $stmt->execute([$event_id, $user_id]);
    
    if ($stmt->rowCount() == 0) {
        header("Location: events.php");
        exit;
    }
    
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

include 'header.php';
?>

<div class="container my-5">
    <div class="mb-4">
        <a href="events.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Events
        </a>
    </div>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <h1 class="mb-3"><?= htmlspecialchars($event['title']) ?></h1>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge bg-primary me-2"><?= htmlspecialchars(ucfirst($event['event_type'] ?? 'Event')) ?></span>
                            <span class="text-muted">Organized by <?= htmlspecialchars($event['username'] ?? 'Anonymous') ?></span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                <span><?= date('F j, Y', strtotime($event['event_date'])) ?></span>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span><?= htmlspecialchars($event['location'] ?? 'TBA') ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        </div>
                        
                        <?php if ($event['is_volunteer']): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-hands-helping me-2"></i>
                                This is a volunteer opportunity. Join to earn volunteer hours!
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Interested?</h5>
                                <p class="card-text">Show your interest in this event</p>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn <?= $event['user_action'] == 'interested' ? 'btn-success' : 'btn-outline-success' ?>" onclick="handleInterest(<?= $event['id'] ?>, 'interested')">
                                        <i class="fas fa-thumbs-up me-2"></i>
                                        Interested (<?= $event['interest_count'] ?? 0 ?>)
                                    </button>
                                    
                                    <button class="btn <?= $event['user_action'] == 'not_interested' ? 'btn-danger' : 'btn-outline-danger' ?>" onclick="handleInterest(<?= $event['id'] ?>, 'not_interested')">
                                        <i class="fas fa-thumbs-down me-2"></i>
                                        Not Interested (<?= $event['not_interested_count'] ?? 0 ?>)
                                    </button>
                                  ?? 0 ?>)
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Share This Event</h5>
                                <div class="d-flex justify-content-around fs-4 mt-3">
                                    <a href="#" class="text-primary"><i class="fab fa-facebook"></i></a>
                                    <a href="#" class="text-info"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="text-success"><i class="fab fa-whatsapp"></i></a>
                                    <a href="#" class="text-secondary"><i class="fas fa-envelope"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function handleInterest(eventId, action) {
    // Check if user is logged in
    <?php if (!isLoggedIn()): ?>
    window.location.href = 'login.php?redirect=event_details.php?id=<?= $event_id ?>';
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

<?php
require_once 'db_connect.php';

// Redirect if not logged in
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// Get organizations for dropdown
try {
    $stmt = $pdo->prepare("SELECT id, name FROM organizations ORDER BY name");
    $stmt->execute();
    $organizations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (Exception $e) {
    $error_message = "Error fetching organizations: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $organization = $_POST['organization'] ?? '';
    $event_type = $_POST['event_type'] ?? '';
    $is_volunteer = isset($_POST['is_volunteer']) ? 1 : 0;
    
    // Validate inputs
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Event title is required";
    }
    
    if (empty($description)) {
        $errors[] = "Event description is required";
    }
    
    if (empty($event_date)) {
        $errors[] = "Event date is required";
    }
    
    if (empty($event_time)) {
        $errors[] = "Event time is required";
    }
    
    if (empty($location)) {
        $errors[] = "Event location is required";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("CALL sp_create_event(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title, $description, $event_date, $event_time, $location, 
                $user_id, $organization, $event_type, $is_volunteer
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            if ($result && isset($result['success']) && $result['success']) {
                $success_message = "Event created successfully!";
                
                // Redirect to event details page after successful creation
                if (isset($result['event_id'])) {
                    header("Refresh: 2; URL=event_details.php?id=" . $result['event_id']);
                } else {
                    header("Refresh: 2; URL=events.php");
                }
            } else {
                $error_message = $result['message'] ?? "Unknown error occurred";
            }
        } catch (Exception $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

include 'header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">Create New Event</h2>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger">
                            <?= $error_message ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required
                                       value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="event_time" class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="event_time" name="event_time" required
                                       value="<?= htmlspecialchars($_POST['event_time'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" required
                                   value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_type" class="form-label">Event Type</label>
                                <select class="form-select" id="event_type" name="event_type">
                                    <option value="workshop" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'workshop') ? 'selected' : '' ?>>Workshop</option>
                                    <option value="charity" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'charity') ? 'selected' : '' ?>>Charity</option>
                                    <option value="food-drive" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'food-drive') ? 'selected' : '' ?>>Food Drive</option>
                                    <option value="competition" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'competition') ? 'selected' : '' ?>>Competition</option>
                                    <option value="festival" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'festival') ? 'selected' : '' ?>>Festival</option>
                                    <option value="academic" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'academic') ? 'selected' : '' ?>>Academic</option>
                                    <option value="networking" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'networking') ? 'selected' : '' ?>>Networking</option>
                                    <option value="other" <?= (isset($_POST['event_type']) && $_POST['event_type'] === 'other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="organization" class="form-label">Organization</label>
                                <select class="form-select" id="organization" name="organization">
                                    <option value="">-- Select Organization --</option>
                                    <?php if (!empty($organizations)): ?>
                                        <?php foreach ($organizations as $org): ?>
                                            <option value="<?= htmlspecialchars($org['name']) ?>" 
                                                    <?= (isset($_POST['organization']) && $_POST['organization'] === $org['name']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($org['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div class="form-text">Provide a detailed description of your event, including what participants can expect.</div>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="is_volunteer" name="is_volunteer" 
                                   <?= (isset($_POST['is_volunteer'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_volunteer">This is a volunteer event</label>
                            <div class="form-text">Check this if participants can earn volunteer hours by attending.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                            <a href="events.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

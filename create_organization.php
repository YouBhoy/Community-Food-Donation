<?php
require_once 'db_connect.php';

// Redirect if not logged in
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get all categories directly from the database
$stmt = $pdo->query("SELECT DISTINCT category FROM organizations ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Add default categories if none exist in the database
if (empty($categories)) {
    $categories = [
        'Academic',
        'Arts and Culture',
        'Community Outreach',
        'Debate',
        'Entrepreneurship',
        'Environmental',
        'Health and Wellness'
    ];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sub_organization = trim($_POST['sub_organization'] ?? '');
    $category = $_POST['category'] ?? 'Academic';
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Organization name is required";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (empty($errors)) {
        // Call the stored procedure to create the organization with transaction handling
        $stmt = $pdo->prepare("CALL sp_create_organization_with_transaction(?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $sub_organization, $category, $user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['success']) {
            $success_message = $result['message'];
            $org_id = $result['organization_id'];
            
            // Redirect to the new organization page after a short delay
            header("Refresh: 2; URL=view_organization.php?id=" . $org_id);
        } else {
            $error_message = $result['message'];
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
                    <h2 class="mb-4">Create New Organization</h2>
                    
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
                            <label for="name" class="form-label">Organization Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" 
                                            <?= (isset($_POST['category']) && $_POST['category'] === $cat) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div class="form-text">Provide a detailed description of your organization, its mission, and activities.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="sub_organization" class="form-label">Sub-organizations (Optional)</label>
                            <textarea class="form-control" id="sub_organization" name="sub_organization" rows="3"><?= htmlspecialchars($_POST['sub_organization'] ?? '') ?></textarea>
                            <div class="form-text">List any sub-organizations or committees, one per line.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Organization</button>
                            <a href="organizations.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
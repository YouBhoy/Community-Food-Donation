<?php
require_once 'db_connect.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'All Categories';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'Name';

$where_clauses = [];
$params = [];

if (!empty($search_query)) {
    $where_clauses[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%" . $search_query . "%";
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($category != 'All Categories') {
    $where_clauses[] = "category = ?";
    $params[] = $category;
}

$sql = "SELECT * FROM organizations";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

if ($sort_by == 'Name') {
    $sql .= " ORDER BY name ASC";
} elseif ($sort_by == 'Members') {
    $sql .= " ORDER BY members DESC";
} elseif ($sort_by == 'Newest') {
    $sql .= " ORDER BY created_at DESC";
} else {
    $sql .= " ORDER BY name ASC";
}

$stmt = $pdo->prepare($sql);
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}
$stmt->execute();
$organizations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [
    'Academic',
    'Arts and Culture',
    'Community Outreach',
    'Debate',
    'Entrepreneurship',
    'Environmental',
    'Health and Wellness'
];

$user_follows = [];
if ($user_id > 0) {
    $follow_stmt = $pdo->prepare("SELECT organization_id FROM user_organization_follows WHERE user_id = ?");
    $follow_stmt->execute([$user_id]);
    $follows = $follow_stmt->fetchAll(PDO::FETCH_COLUMN);
    $user_follows = array_flip($follows); 
}

include 'header.php';
?>

<div class="hero">
    <div class="container text-center">
        <h1>Organizations</h1>
        <p class="lead">Discover and connect with student organizations across campus.</p>
    </div>
</div>

<div class="container my-5">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Browse Organizations</h2>
        <?php if (isLoggedIn()): ?>
            <a href="create_organization.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Organization
            </a>
        <?php endif; ?>
    </div>

    <form id="search-form" method="GET" action="organizations.php">
        <div class="row mb-4 align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <span class="fw-medium">Filters</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-0">
                    <label class="form-label small text-muted mb-1">Category</label>
                    <select class="form-select" name="category" id="category-filter">
                        <option <?= $category === 'All Categories' ? 'selected' : '' ?>>All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-0">
                    <label class="form-label small text-muted mb-1">Sort By</label>
                    <select class="form-select" name="sort_by" id="sort-filter">
                        <option value="Name" <?= $sort_by === 'Name' ? 'selected' : '' ?>>Name</option>
                        <option value="Members" <?= $sort_by === 'Members' ? 'selected' : '' ?>>Members</option>
                        <option value="Newest" <?= $sort_by === 'Newest' ? 'selected' : '' ?>>Newest</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-0">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="search-input" placeholder="Search organizations..." value="<?= htmlspecialchars($search_query) ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php if (!empty($search_query)): ?>
    <div class="alert alert-light mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Search results for: <strong><?= htmlspecialchars($search_query) ?></strong>
            </div>
            <a href="organizations.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Clear
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <?php 
        if (!empty($organizations)):
            foreach ($organizations as $org): 
                $isFollowing = isset($user_follows[$org['id']]);
                
                $firstLetter = substr($org['name'], 0, 1);
                $category = $org['category'] ?? 'Academic';
        ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="org-avatar me-3">
                                <?= $firstLetter ?>
                            </div>
                            <div>
                                <h5 class="card-title mb-0"><?= htmlspecialchars($org['name']) ?></h5>
                                <span class="badge bg-light text-dark"><?= htmlspecialchars($category) ?></span>
                            </div>
                        </div>
                        
                        <p class="card-text small text-muted mb-3">
                            <?= htmlspecialchars(substr($org['description'], 0, 100)) ?>...
                        </p>
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-user-friends text-muted me-1 small"></i>
                            <span class="small text-muted"><?= htmlspecialchars($org['members']) ?> members</span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="view_organization.php?id=<?= $org['id'] ?>" class="btn btn-outline-primary flex-grow-1">View Profile</a>
                            <button 
                                class="btn <?= $isFollowing ? 'btn-primary' : 'btn-outline-primary' ?> follow-btn flex-grow-1" 
                                data-org-id="<?= $org['id'] ?>"
                                data-following="<?= $isFollowing ? '1' : '0' ?>">
                                <?= $isFollowing ? 'Followed' : 'Follow' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            endforeach;
        else: 
        ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <h4>No organizations found</h4>
                <?php if (!empty($search_query)): ?>
                    <p class="text-muted">Try different keywords or browse all organizations</p>
                    <a href="organizations.php" class="btn btn-primary mt-2">View All Organizations</a>
                <?php else: ?>
                    <p class="text-muted">Be the first to create an organization!</p>
                    <?php if (isLoggedIn()): ?>
                        <a href="create_organization.php" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-2"></i>Create Organization
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.follow-btn').click(function() {
        <?php if (!isLoggedIn()): ?>
        window.location.href = 'login.php?redirect=organizations.php';
        return;
        <?php endif; ?>
        
        const button = $(this);
        const orgId = button.data('org-id');
        const isFollowing = button.data('following') === '1';
        
        $.ajax({
            url: 'follow_organization.php',
            type: 'POST',
            data: {
                org_id: orgId,
                action: isFollowing ? 'unfollow' : 'follow'
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    if (isFollowing) {
                        button.removeClass('btn-primary').addClass('btn-outline-primary');
                        button.text('Follow');
                        button.data('following', '0');
                    } else {
                        button.removeClass('btn-outline-primary').addClass('btn-primary');
                        button.text('Followed');
                        button.data('following', '1');
                    }
                    
                    if (data.members_count !== undefined) {
                        const membersElement = button.closest('.card-body').find('.text-muted:contains("members")');
                        membersElement.text(data.members_count + ' members');
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    $('#category-filter, #sort-filter').change(function() {
        $('#search-form').submit();
    });
});
</script>

<?php include 'footer.php'; ?>

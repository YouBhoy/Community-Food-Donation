<?php
require_once 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM organizations WHERE id = ?");
$stmt->execute([$id]);
$org = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$org) {
    header("Location: organizations.php");
    exit;
}

$isFollowing = false;
if ($user_id > 0) {
    $follow_stmt = $pdo->prepare("SELECT COUNT(*) FROM user_organization_follows WHERE user_id = ? AND organization_id = ?");
    $follow_stmt->execute([$user_id, $id]);
    $isFollowing = ($follow_stmt->fetchColumn() > 0);
}

$firstLetter = substr($org['name'], 0, 1);
$category = $org['category'] ?? 'Academic';

include 'header.php';
?>

<div class="container my-5">
    <div class="mb-4">
        <a href="organizations.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Organizations
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-4">
                        <div class="org-avatar me-3" style="width: 64px; height: 64px; font-size: 1.75rem;">
                            <?= $firstLetter ?>
                        </div>
                        <div>
                            <h2 class="mb-1"><?= htmlspecialchars($org['name']) ?></h2>
                            <span class="badge bg-light text-dark"><?= htmlspecialchars($category) ?></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">Description</h5>
                        <p><?= nl2br(htmlspecialchars($org['description'])) ?></p>
                    </div>

                    <?php if (!empty($org['sub_organization'])): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">Sub-organizations</h5>
                        <p><?= nl2br(htmlspecialchars($org['sub_organization'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Organization Info</h5>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user-friends text-muted me-3"></i>
                                <div>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($org['members']) ?> members</p>
                                </div>
                            </div>
                            
                            <button 
                                class="btn <?= $isFollowing ? 'btn-primary' : 'btn-outline-primary' ?> follow-btn w-100" 
                                data-org-id="<?= $org['id'] ?>"
                                data-following="<?= $isFollowing ? '1' : '0' ?>">
                                <?= $isFollowing ? 'Followed' : 'Follow' ?>
                            </button>
                            
                            <?php if (isLoggedIn()): ?>
                            <a href="manage_organization.php?id=<?= $org['id'] ?>" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-edit me-2"></i>Edit Organization
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Contact</h5>
                            <p class="mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:<?= strtolower(str_replace(' ', '', $org['name'])) ?>@example.com" class="text-decoration-none">
                                    <?= strtolower(str_replace(' ', '', $org['name'])) ?>@example.com
                                </a>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-globe text-muted me-2"></i>
                                <a href="#" class="text-decoration-none">
                                    connecthub.com/<?= strtolower(str_replace(' ', '-', $org['name'])) ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.follow-btn').click(function() {
        <?php if (!isLoggedIn()): ?>
        window.location.href = 'login.php?redirect=view_organization.php?id=<?= $id ?>';
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
                        const membersElement = $('.fw-medium:contains("members")');
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
});
</script>

<?php include 'footer.php'; ?>

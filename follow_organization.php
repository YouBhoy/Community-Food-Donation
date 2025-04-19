<?php
require_once 'db_connect.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if required parameters are provided
if (!isset($_POST['org_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$org_id = (int)$_POST['org_id'];
$action = $_POST['action'];

// Validate organization exists
$check_stmt = $pdo->prepare("SELECT COUNT(*) FROM organizations WHERE id = ?");
$check_stmt->execute([$org_id]);
if ($check_stmt->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'message' => 'Organization not found']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    if ($action === 'follow') {
        // Check if already following
        $check_follow = $pdo->prepare("SELECT COUNT(*) FROM user_organization_follows WHERE user_id = ? AND organization_id = ?");
        $check_follow->execute([$user_id, $org_id]);
        
        if ($check_follow->fetchColumn() == 0) {
            // Follow organization
            $follow_stmt = $pdo->prepare("INSERT INTO user_organization_follows (user_id, organization_id, followed_at) VALUES (?, ?, NOW())");
            $follow_stmt->execute([$user_id, $org_id]);
            
            // Update member count
            $update_stmt = $pdo->prepare("UPDATE organizations SET members = members + 1 WHERE id = ?");
            $update_stmt->execute([$org_id]);
        }
    } else if ($action === 'unfollow') {
        // Unfollow organization
        $unfollow_stmt = $pdo->prepare("DELETE FROM user_organization_follows WHERE user_id = ? AND organization_id = ?");
        $unfollow_stmt->execute([$user_id, $org_id]);
        
        // Update member count
        $update_stmt = $pdo->prepare("UPDATE organizations SET members = GREATEST(members - 1, 0) WHERE id = ?");
        $update_stmt->execute([$org_id]);
    } else {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
    
    // Get updated member count
    $count_stmt = $pdo->prepare("SELECT members FROM organizations WHERE id = ?");
    $count_stmt->execute([$org_id]);
    $members_count = $count_stmt->fetchColumn();
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'action' => $action,
        'members_count' => $members_count
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

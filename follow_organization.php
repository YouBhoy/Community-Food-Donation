<?php
require_once 'db_connect.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['org_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$org_id = (int)$_POST['org_id'];
$action = $_POST['action'];

try {
    $check_stmt = $pdo->prepare("CALL sp_check_organization_exists(?)");
    $check_stmt->execute([$org_id]);
    $exists = $check_stmt->fetchColumn();
    $check_stmt->closeCursor();
    
    if (!$exists) {
        echo json_encode(['success' => false, 'message' => 'Organization not found']);
        exit;
    }
    
    if ($action === 'follow') {
        $stmt = $pdo->prepare("CALL sp_follow_organization(?, ?)");
        $stmt->execute([$user_id, $org_id]);
    } else if ($action === 'unfollow') {
        $stmt = $pdo->prepare("CALL sp_unfollow_organization(?, ?)");
        $stmt->execute([$user_id, $org_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
    $stmt->closeCursor();
    
    $count_stmt = $pdo->prepare("CALL sp_get_organization_member_count(?)");
    $count_stmt->execute([$org_id]);
    $members_count = $count_stmt->fetchColumn();
    $count_stmt->closeCursor();
    
    echo json_encode([
        'success' => true, 
        'action' => $action,
        'members_count' => $members_count
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

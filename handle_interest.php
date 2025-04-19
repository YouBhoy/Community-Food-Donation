<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get JSON data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['event_id']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$event_id = (int)$data['event_id'];
$action = $data['action'];
$user_id = $_SESSION['user_id'];

if ($action !== 'interested' && $action !== 'not_interested') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Check if interest record already exists
    $check_sql = "SELECT id, action FROM interests WHERE event_id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$event_id, $user_id]);
    $interest = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($interest) {
        if ($interest['action'] === $action) {
            // If same action, delete the record (toggle off)
            $delete_sql = "DELETE FROM interests WHERE id = ?";
            $delete_stmt = $pdo->prepare($delete_sql);
            $delete_stmt->execute([$interest['id']]);
        } else {
            // If different action, update the record
            $update_sql = "UPDATE interests SET action = ? WHERE id = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$action, $interest['id']]);
        }
    } else {
        // If no record exists, insert new one
        $insert_sql = "INSERT INTO interests (event_id, user_id, action, created_at) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->execute([$event_id, $user_id, $action]);
    }
    
    // Get updated counts
    $count_sql = "SELECT 
                    (SELECT COUNT(*) FROM interests WHERE event_id = ? AND action = 'interested') AS interest_count,
                    (SELECT COUNT(*) FROM interests WHERE event_id = ? AND action = 'not_interested') AS not_interested_count";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute([$event_id, $event_id]);
    $counts = $count_stmt->fetch(PDO::FETCH_ASSOC);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'interest_count' => $counts['interest_count'],
        'not_interested_count' => $counts['not_interested_count']
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

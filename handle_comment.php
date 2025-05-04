<?php
require_once 'db_connect.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['event_id']) || !isset($data['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$event_id = (int)$data['event_id'];
$comment = trim($data['comment']);
$user_id = $_SESSION['user_id'];

// Validate comment
if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    exit;
}

try {
    // Using stored procedure for adding a comment
    $stmt = $pdo->prepare("CALL sp_add_event_comment(?, ?, ?)");
    $stmt->execute([$event_id, $user_id, $comment]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    if ($result && isset($result['success']) && $result['success']) {
        echo json_encode([
            'success' => true, 
            'message' => $result['message'],
            'comment_id' => $result['comment_id']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

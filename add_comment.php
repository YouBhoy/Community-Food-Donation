<?php
require_once 'db_connect.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to comment']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and decode the JSON data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validate required fields
if (!isset($data['event_id']) || !isset($data['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Sanitize and validate input
$event_id = (int)$data['event_id'];
$user_id = (int)$_SESSION['user_id'];
$comment = trim($data['comment']);

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    exit;
}

try {
    // Call the stored procedure to add a comment
    $stmt = $pdo->prepare("CALL sp_add_comment(?, ?, ?)");
    $stmt->execute([$event_id, $user_id, $comment]);
    
    // Get the result (new comment data)
    $comment_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    if ($comment_data) {
        echo json_encode([
            'success' => true, 
            'message' => 'Comment added successfully',
            'comment' => $comment_data
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

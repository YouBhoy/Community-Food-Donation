<?php
require_once 'db_connect.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to delete a comment']);
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
if (!isset($data['comment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing comment ID']);
    exit;
}

// Sanitize input
$comment_id = (int)$data['comment_id'];
$user_id = (int)$_SESSION['user_id'];

try {
    // Call the stored procedure to delete a comment
    $stmt = $pdo->prepare("CALL sp_delete_comment(?, ?)");
    $stmt->execute([$comment_id, $user_id]);
    
    // Get the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    echo json_encode([
        'success' => $result['success'], 
        'message' => $result['message']
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

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
    $stmt = $pdo->prepare("CALL sp_handle_event_interest(?, ?, ?)");
    $stmt->execute([$event_id, $user_id, $action]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    echo json_encode([
        'success' => true, 
        'interest_count' => $counts['interest_count'],
        'not_interested_count' => $counts['not_interested_count']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

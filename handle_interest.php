<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$eventId = $data['event_id'];
$userId = $_SESSION['user_id'];
$action = $data['action'];

$check = $conn->prepare("SELECT * FROM event_interactions 
                         WHERE event_id = ? AND user_id = ?");
$check->bind_param("ii", $eventId, $userId);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE event_interactions 
                            SET is_interested = ? 
                            WHERE event_id = ? AND user_id = ?");
    $isInterested = ($action === 'interested') ? 1 : 0;
    $stmt->bind_param("iii", $isInterested, $eventId, $userId);
} else {
    $stmt = $conn->prepare("INSERT INTO event_interactions 
                            (event_id, user_id, is_interested) 
                            VALUES (?, ?, ?)");
    $isInterested = ($action === 'interested') ? 1 : 0;
    $stmt->bind_param("iii", $eventId, $userId, $isInterested);
}

$stmt->execute();

$counts = [
    'interested' => $conn->query("SELECT COUNT(*) FROM event_interactions 
                                  WHERE event_id = $eventId AND is_interested = 1")->fetch_row()[0],
    'not_interested' => $conn->query("SELECT COUNT(*) FROM event_interactions 
                                      WHERE event_id = $eventId AND is_interested = 0")->fetch_row()[0]
];

echo json_encode(['count' => $counts[$action]]);
?>
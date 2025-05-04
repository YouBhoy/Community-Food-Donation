try {
    // Using stored procedure to get event details
    $stmt = $pdo->prepare("CALL sp_get_event_details(?, ?)");
    $stmt->execute([$event_id, $user_id]);
    
    if ($stmt->rowCount() == 0) {
        header("Location: events.php");
        exit;
    }
    
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    // Using stored procedure to get comments for this event
    $stmt = $pdo->prepare("CALL sp_get_event_comments(?)");
    $stmt->execute([$event_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

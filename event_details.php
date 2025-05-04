<?php
require_once 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit;
}

$event_id = (int)$_GET['id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

try {
    // Get event details
    $stmt = $pdo->prepare("CALL sp_get_event_details(?, ?)");
    $stmt->execute([$event_id, $user_id]);
    
    if ($stmt->rowCount() == 0) {
        header("Location: events.php");
        exit;
    }
    
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    // Get comments for this event
    $stmt = $pdo->prepare("CALL sp_get_event_comments(?)");
    $stmt->execute([$event_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

include 'header.php';
?>

<div class="container my-5">
    <div class="mb-4">
        <a href="events.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Events
        </a>
    </div>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <h1 class="mb-3"><?= htmlspecialchars($event['title']) ?></h1>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge bg-primary me-2"><?= htmlspecialchars(ucfirst($event['event_type'] ?? 'Event')) ?></span>
                            <span class="text-muted">Organized by <?= htmlspecialchars($event['username'] ?? 'Anonymous') ?></span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                <span><?= date('F j, Y', strtotime($event['event_date'])) ?></span>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span><?= htmlspecialchars($event['location'] ?? 'TBA') ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        </div>
                        
                        <?php if (isset($event['is_volunteer']) && $event['is_volunteer']): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-hands-helping me-2"></i>
                                This is a volunteer opportunity. Join to earn volunteer hours!
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Interested?</h5>
                                <p class="card-text">Show your interest in this event</p>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn <?= isset($event['user_action']) && $event['user_action'] == 'interested' ? 'btn-success' : 'btn-outline-success' ?>" onclick="handleInterest(<?= $event['id'] ?>, 'interested')">
                                        <i class="fas fa-thumbs-up me-2"></i>
                                        Interested (<?= $event['interest_count'] ?? 0 ?>)
                                    </button>
                                    
                                    <button class="btn <?= isset($event['user_action']) && $event['user_action'] == 'not_interested' ? 'btn-danger' : 'btn-outline-danger' ?>" onclick="handleInterest(<?= $event['id'] ?>, 'not_interested')">
                                        <i class="fas fa-thumbs-down me-2"></i>
                                        Not Interested (<?= $event['not_interested_count'] ?? 0 ?>)
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Share This Event</h5>
                                <div class="d-flex justify-content-around fs-4 mt-3">
                                    <a href="#" class="text-primary"><i class="fab fa-facebook"></i></a>
                                    <a href="#" class="text-info"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="text-success"><i class="fab fa-whatsapp"></i></a>
                                    <a href="#" class="text-secondary"><i class="fas fa-envelope"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Comments</h4>
            </div>
            <div class="card-body">
                <?php if (isLoggedIn()): ?>
                    <div class="mb-4">
                        <form id="comment-form">
                            <div class="mb-3">
                                <textarea class="form-control" id="comment-text" rows="3" placeholder="Write a comment..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-4">
                        <a href="login.php?redirect=event_details.php?id=<?= $event_id ?>" class="alert-link">Login</a> to post a comment.
                    </div>
                <?php endif; ?>
                
                <div id="comments-container">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item mb-3 p-3 border rounded" id="comment-<?= $comment['id'] ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-bold"><?= htmlspecialchars($comment['username']) ?></span>
                                        <span class="badge bg-secondary ms-2"><?= ucfirst(htmlspecialchars($comment['role'])) ?></span>
                                    </div>
                                    <small class="text-muted"><?= date('M d, Y g:i A', strtotime($comment['created_at'])) ?></small>
                                </div>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                                
                                <?php if (isLoggedIn() && ($comment['user_id'] == $_SESSION['user_id'] || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'))): ?>
                                    <div class="text-end">
                                        <button class="btn btn-sm btn-outline-danger delete-comment" data-comment-id="<?= $comment['id'] ?>">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div id="no-comments-message" class="text-center py-4">
                            <p class="text-muted mb-0">No comments yet. Be the first to comment!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function handleInterest(eventId, action) {
    // Check if user is logged in
    <?php if (!isLoggedIn()): ?>
    window.location.href = 'login.php?redirect=event_details.php?id=<?= $event_id ?>';
    return;
    <?php endif; ?>
    
    fetch('handle_interest.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Comment functionality
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isLoggedIn()): ?>
    const commentForm = document.getElementById('comment-form');
    const commentText = document.getElementById('comment-text');
    const commentsContainer = document.getElementById('comments-container');
    const noCommentsMessage = document.getElementById('no-comments-message');
    
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const comment = commentText.value.trim();
        if (!comment) {
            alert('Please enter a comment');
            return;
        }
        
        fetch('add_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                event_id: <?= $event_id ?>, 
                comment: comment 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the form
                commentText.value = '';
                
                // Remove no comments message if it exists
                if (noCommentsMessage) {
                    noCommentsMessage.remove();
                }
                
                // Add the new comment to the top of the list
                const newComment = document.createElement('div');
                newComment.className = 'comment-item mb-3 p-3 border rounded';
                newComment.id = 'comment-' + data.comment.id;
                
                newComment.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold">${data.comment.username}</span>
                            <span class="badge bg-secondary ms-2">${data.comment.role.charAt(0).toUpperCase() + data.comment.role.slice(1)}</span>
                        </div>
                        <small class="text-muted">${new Date(data.comment.created_at).toLocaleString()}</small>
                    </div>
                    <p class="mb-1">${data.comment.comment.replace(/\n/g, '<br>')}</p>
                    <div class="text-end">
                        <button class="btn btn-sm btn-outline-danger delete-comment" data-comment-id="${data.comment.id}">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </div>
                `;
                
                // Add the new comment to the top
                commentsContainer.insertBefore(newComment, commentsContainer.firstChild);
                
                // Add event listener to the delete button
                const deleteButton = newComment.querySelector('.delete-comment');
                if (deleteButton) {
                    deleteButton.addEventListener('click', handleDeleteComment);
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while posting your comment');
        });
    });
    
    // Add event listeners to delete buttons
    const deleteButtons = document.querySelectorAll('.delete-comment');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleDeleteComment);
    });
    
    function handleDeleteComment(e) {
        if (!confirm('Are you sure you want to delete this comment?')) {
            return;
        }
        
        const commentId = this.getAttribute('data-comment-id');
        
        fetch('delete_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment_id: commentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the comment from the DOM
                const commentElement = document.getElementById('comment-' + commentId);
                if (commentElement) {
                    commentElement.remove();
                }
                
                // If no more comments, show the no comments message
                if (commentsContainer.children.length === 0) {
                    const noCommentsDiv = document.createElement('div');
                    noCommentsDiv.id = 'no-comments-message';
                    noCommentsDiv.className = 'text-center py-4';
                    noCommentsDiv.innerHTML = '<p class="text-muted mb-0">No comments yet. Be the first to comment!</p>';
                    commentsContainer.appendChild(noCommentsDiv);
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the comment');
        });
    }
    <?php endif; ?>
});
</script>

<?php include 'footer.php'; ?>

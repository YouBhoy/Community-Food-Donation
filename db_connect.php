<?php
$host = 'localhost';
$dbname = 'connecthub';
$username = 'root'; 
$password = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // For mysqli compatibility (used in some files)
    $mysqli = new mysqli($host, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        throw new Exception("mysqli connection failed: " . $mysqli->connect_error);
    }
    
    // For legacy code using $conn
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Common functions used across the site
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirectIfNotLoggedIn($redirect = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect");
        exit;
    }
}

function getLatestEvents($pdo, $limit = 3) {
    try {
        $stmt = $pdo->prepare("SELECT event_id, title, description, event_date, event_time, event_type 
                               FROM events 
                               ORDER BY event_date DESC, event_time DESC 
                               LIMIT ?");
                               
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

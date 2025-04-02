<?php
session_start();
$conn = new mysqli("localhost", "root", "", "food_donation_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: home.php"); 
    } else {
        header("Location: login.php?error=invalid_credentials");
    }
} else {
    header("Location: login.php?error=user_not_found");
}

$stmt->close();
$conn->close();
?>
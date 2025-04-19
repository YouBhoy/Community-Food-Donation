<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $graduation_year = $_POST['graduation_year'];
    
    $errors = [];
    
    if (empty($first_name) || empty($last_name)) {
        $errors[] = "Name fields cannot be empty";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($password)) {
        $errors[] = "Password cannot be empty";
    }
    
    if (!isset($_POST['terms'])) {
        $errors[] = "You must agree to the Terms of Service";
    }
    
    if (!empty($errors)) {
        header("Location: register.php?error=" . urlencode(implode(", ", $errors)));
        exit;
    }
    
    try {
        $check_email_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check_email_stmt->execute([$email]);
        
        if ($check_email_stmt->fetchColumn() > 0) {
            header("Location: register.php?error=" . urlencode("Email already exists. Please use a different email or login."));
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, graduation_year) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $password, $role, $graduation_year]);
        
        header("Location: login.php?success=Registration successful! Please login.");
        exit;
    } catch (PDOException $e) {
        header("Location: register.php?error=Registration failed: " . urlencode($e->getMessage()));
        exit;
    }
}
?>

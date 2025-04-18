<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $graduation_year = $_POST['graduation_year'];
    
    // Validate input
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
    
    // Check if email already exists
    $check_sql = "SELECT COUNT(*) FROM users WHERE email = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$email]);
    
    if ($check_stmt->fetchColumn() > 0) {
        $errors[] = "Email already exists. Please use a different email or login.";
    }
    
    if (!empty($errors)) {
        header("Location: register.php?error=" . urlencode(implode(", ", $errors)));
        exit;
    }
    
    // Insert new user
    try {
        $sql = "INSERT INTO users (first_name, last_name, email, password, role, graduation_year) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $last_name, $email, $password, $role, $graduation_year]);
        
        header("Location: login.php?success=Registration successful! Please login.");
        exit;
    } catch (PDOException $e) {
        header("Location: register.php?error=Registration failed: " . urlencode($e->getMessage()));
        exit;
    }
}
?>

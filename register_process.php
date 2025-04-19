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
    
    if (!empty($errors)) {
        header("Location: register.php?error=" . urlencode(implode(", ", $errors)));
        exit;
    }
    
    try {
        // Check if the stored procedure exists
        $check_proc_stmt = $pdo->prepare("CALL sp_check_procedure_exists('sp_register_user')");
        $check_proc_stmt->execute();
        $proc_exists = $check_proc_stmt->fetch(PDO::FETCH_ASSOC)['procedure_exists'] > 0;
        $check_proc_stmt->closeCursor();
        
        if ($proc_exists) {
            // Check if email already exists using sp_authenticate_user
            $check_email_stmt = $pdo->prepare("CALL sp_authenticate_user(?)");
            $check_email_stmt->execute([$email]);
            
            if ($check_email_stmt->rowCount() > 0) {
                header("Location: register.php?error=" . urlencode("Email already exists. Please use a different email or login."));
                exit;
            }
            $check_email_stmt->closeCursor();
            
            // Register the user using stored procedure
            $stmt = $pdo->prepare("CALL sp_register_user(?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $password, $role, $graduation_year]);
            
            header("Location: login.php?success=Registration successful! Please login.");
            exit;
        } else {
            header("Location: register.php?error=" . urlencode("Required stored procedure does not exist. Please contact the administrator."));
            exit;
        }
    } catch (PDOException $e) {
        header("Location: register.php?error=Registration failed: " . urlencode($e->getMessage()));
        exit;
    }
}
?>

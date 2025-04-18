<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['username']; 
    $password = $_POST['password']; 
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'dashboard.php';
   
    $error_message = '';
    
    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in both fields.';
    }
    
    if (empty($error_message)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Set remember me cookie if requested
            if (isset($_POST['remember'])) {
                setcookie('username', $email, time() + 86400 * 30, "/"); 
            }
            
            header("Location: $redirect");
            exit;
        } else {
            $error_message = 'Invalid email or password.';
        }
    }
    
    if ($error_message) {
        header("Location: login.php?error=" . urlencode($error_message) . "&redirect=" . urlencode($redirect));
        exit;
    }
}
?>

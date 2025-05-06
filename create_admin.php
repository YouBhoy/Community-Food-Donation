<?php
// Database connection details
$host = 'localhost';
$dbname = 'connecthub';
$username = 'root'; 
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Admin details
    $first_name = 'Admin';
    $last_name = 'User';
    $email = 'admin@example.com';
    // Generate bcrypt hash for password 'admin123'
    $hashed_password = password_hash('admin123', PASSWORD_BCRYPT);
    $role = 'admin';
    
    // Check if admin already exists
    $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->execute([$email]);
    $existing_user = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_user) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password = ?, first_name = ?, last_name = ?, role = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $first_name, $last_name, $role, $email]);
        echo "Admin user updated successfully!";
    } else {
        // Insert new admin
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $hashed_password, $role]);
        echo "Admin user created successfully!";
    }
    
    echo "<br><br>Email: admin@example.com<br>Password: admin123";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
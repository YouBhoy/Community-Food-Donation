<?php
require_once 'db_connect.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Check if there's a redirect parameter
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';

include 'header.php';
?>

<div class="form-box">
    <h1>ConnectHub</h1>
    <h2>Welcome back</h2>
    <p>Sign in to your account to continue your journey</p>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>
    
    <form action="auth.php" method="POST">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        
        <label for="email">Email</label>
        <input type="email" id="email" name="username" placeholder="Email" required>
        
        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="button" onclick="togglePassword()">👁</button>
        </div>
        <a href="#" class="forgot-password">Forgot password?</a>
        
        <div class="checkbox-container">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </div>
        
        <button type="submit">Sign in</button>
    </form>
    <p>Don't have an account? <a href="register.php">Sign up</a></p>
</div>

<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>

<?php include 'footer.php'; ?>

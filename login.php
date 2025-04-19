<?php
require_once 'db_connect.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - ConnectHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form-container">
        <div class="auth-form">
            <div class="auth-brand">ConnectHub</div>
            <h2>Welcome back</h2>
            <p class="auth-subtitle">Sign in to your account to continue your journey</p>
            
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
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="username" placeholder="your.email@example.edu" required>
                </div>
                
                <div class="form-group">
                    <div class="password-header">
                        <label for="password">Password</label>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn-auth">Sign in</button>
            </form>
            <p class="auth-footer">Don't have an account? <a href="register.php">Sign up</a></p>
            <p class="auth-footer mt-2"><a href="index.php"><i class="fas fa-home me-1"></i>Back to Home</a></p>
        </div>
    </div>
    
    <div class="auth-info">
        <div class="auth-info-content">
            <h2>Community ConnectHub</h2>
            <p>A centralized platform for university-based organizations to collaborate, share resources, and create meaningful partnerships.</p>
            
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Connect with Organizations</h4>
                        <p>Discover organizations that align with your interests and goals.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Discover Events</h4>
                        <p>Stay updated on campus events, workshops, and volunteer opportunities.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Make an Impact</h4>
                        <p>Contribute to initiatives that support SDG 17: Partnerships for the Goals.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Track Your Involvement</h4>
                        <p>Manage your participation in organizations, events, and initiatives through a personalized dashboard.</p>
                    </div>
                </div>
            </div>
            
            <div class="sdg-badge">
                <span>Aligned with SDG 17</span>
            </div>
            
            <a href="about.php" class="learn-more-btn">Learn more</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    var toggleBtn = document.querySelector(".password-toggle i");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleBtn.classList.remove("fa-eye");
        toggleBtn.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        toggleBtn.classList.remove("fa-eye-slash");
        toggleBtn.classList.add("fa-eye");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

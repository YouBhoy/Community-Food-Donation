<?php
require_once 'db_connect.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - ConnectHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form-container">
        <div class="auth-form">
            <div class="auth-brand">ConnectHub</div>
            <h2>Create your account</h2>
            <p class="auth-subtitle">Join our community of research organizations</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <form action="register_process.php" method="POST">
                <div class="form-row">
                    <div class="form-group half">
                        <label for="first_name">First name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="John" required>
                    </div>
                    <div class="form-group half">
                        <label for="last_name">Last name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Doe" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="your.email@university.edu" required>
                    <p class="input-hint">Please use your university email address</p>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="role">I am a</label>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="graduation_year">Expected Graduation Year</label>
                    <select id="graduation_year" name="graduation_year" required>
                        <?php for ($year = date('Y'); $year <= date('Y') + 10; $year++) : ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-check terms-check">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms">I agree to the <a href="#" class="link">Terms of Service</a> and <a href="#" class="link">Privacy Policy</a></label>
                </div>
                
                <button type="submit" class="btn-auth">Create account</button>
            </form>
            <p class="auth-footer">Already have an account? <a href="login.php">Sign in</a></p>
            <p class="auth-footer mt-2"><a href="index.php"><i class="fas fa-home me-1"></i>Back to Home</a></p>
        </div>
    </div>
    
    <div class="auth-info">
        <div class="auth-info-content">
            <h2>Why Join Community ConnectHub?</h2>
            <p>Community ConnectHub is a comprehensive platform designed to foster collaboration and partnerships among university-based organizations, aligned with SDG 17.</p>
            
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Discover & Connect</h4>
                        <p>Find and join organizations that match your interests, connect with like-minded individuals, and build your campus network.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Events & Activities</h4>
                        <p>Stay informed about campus events, workshops, and activities. Register, save, and manage your event calendar in one place.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Track & Showcase</h4>
                        <p>Document your involvement, track volunteer hours, and build a portfolio of your participation in campus organizations.</p>
                    </div>
                </div>
            </div>
            
            <div class="sdg-section">
                <h3>SDG 17: Partnerships for the Goals</h3>
                <p>Community ConnectHub is aligned with Sustainable Development Goal 17, recognizing the importance of partnerships to achieve sustainable development. Our platform is part of a university-wide initiative to develop collaborative partnerships for social positive change on campus and beyond.</p>
                
                <div class="sdg-badge">
                    <span>Aligned with SDG 17: Partnerships for sustainable development</span>
                </div>
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

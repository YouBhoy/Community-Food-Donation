<?php
require_once 'db_connect.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

include 'header.php';
?>

<div class="form-box">
    <h1>ConnectHub</h1>
    <h2>Create your account</h2>
    <p>Join our community of university organizations</p>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    
    <form action="register_process.php" method="POST">
        <div class="name-container">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
            </div>
        </div>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <p class="note">Please use your university email address</p>
        
        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="button" onclick="togglePassword()">👁</button>
        </div>
        
        <label for="role">I am a</label>
        <select id="role" name="role" required>
            <option value="student">Student</option>
            <option value="staff">Staff</option>
        </select>
        
        <label for="graduation_year">Expected Graduation Year</label>
        <select id="graduation_year" name="graduation_year" required>
            <?php for ($year = date('Y'); $year <= date('Y') + 10; $year++) : ?>
                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
            <?php endfor; ?>
        </select>
        
        <div class="checkbox-container">
            <input type="checkbox" name="terms" id="terms" required>
            <label for="terms">I agree to the <a href="#" class="link">Terms of Service</a> and <a href="#" class="link">Privacy Policy</a></label>
        </div>
        
        <button type="submit">Create account</button>
    </form>
    <p>Already have an account? <a href="login.php" class="link">Sign in</a></p>
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

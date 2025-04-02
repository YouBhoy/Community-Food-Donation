<div class="form-box">
    <h1>Register</h1>
    
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'email_exists'): ?>
            <p style="color: red;">Email already registered!</p>
        <?php elseif ($_GET['error'] === 'username_exists'): ?>
            <p style="color: red;">Username already taken!</p>
        <?php else: ?>
            <p style="color: red;">Registration failed. Please try again.</p>
        <?php endif; ?>
    <?php endif; ?>
    
    <form action="register_process.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button>Register</button>
    </form>
    <a href="login.php">Already have an account?</a>
</div>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-box">
        <h1>Login</h1>
        <?php if (isset($_GET['error'])) : ?>
            <p style="color: red;">Invalid username or password!</p>
        <?php endif; ?>
        <form action="auth.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button>Login</button>
        </form>
        <a href="register.php">Create account</a>
    </div>
</body>
</html>
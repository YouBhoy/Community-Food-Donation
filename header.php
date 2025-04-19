<?php
require_once 'db_connect.php';

// Determine active page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ConnectHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">ConnectHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>" href="about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'events.php' ? 'active' : '' ?>" href="events.php">Events</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'organizations.php' ? 'active' : '' ?>" href="organizations.php">Organizations</a>
        </li>
      </ul>
      <div class="ms-3">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="dropdown">
            <a href="#" class="btn btn-outline-primary dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
              <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a class="dropdown-item" href="admin/index.php">Admin Panel</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary">Login</a>
          <a href="register.php" class="btn btn-primary ms-2">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

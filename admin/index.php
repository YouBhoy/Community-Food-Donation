<?php
require_once '../db_connect.php';


$stmt = $pdo->prepare("CALL sp_get_all_users()");
$stmt->execute();
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$stmt = $pdo->prepare("CALL sp_get_users_by_role(?)");
$stmt->execute(['admin']);    
$adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$stmt = $pdo->prepare("CALL sp_get_users_by_role_not(?)");
$stmt->execute(['admin']);
$regularUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);  

$stmt = $pdo->prepare("CALL sp_get_all_organizations()");
$stmt->execute();
$organizations = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - ConnectHub</title>
  <link rel="stylesheet" href="../styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <span class="sidebar-title">ConnectHub Admin</span>
        <button class="sidebar-toggle" id="sidebar-close">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <div class="sidebar-content">
        <nav class="sidebar-nav">
          <a href="#dashboard" class="sidebar-link active">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
          </a>
          <a href="#users" class="sidebar-link">
            <i class="fas fa-users"></i>
            <span>Manage Users</span>
            <span class="badge"><?php echo count($allUsers); ?></span>
          </a>
          <a href="#events" class="sidebar-link">
            <i class="fas fa-calendar"></i>
            <span>Manage Events</span>
          </a>
          <a href="#organizations" class="sidebar-link">
            <i class="fas fa-building"></i>
            <span>Manage Organizations</span>
          </a>
          <a href="#reports" class="sidebar-link">
            <i class="fas fa-chart-bar"></i>
            <span>Reports & Analytics</span>
          </a>
          <a href="#settings" class="sidebar-link">
            <i class="fas fa-cog"></i>
            <span>System Settings</span>
          </a>
        </nav>
        <div class="sidebar-footer">
          <a href="../index.php" class="sidebar-link">
            <i class="fas fa-home"></i>
            <span>Back to Site</span>
          </a>
          <a href="../logout.php" class="sidebar-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </a>
        </div>
      </div>
    </aside>

<!-- Main Content -->
    <main class="main-content"> 
<!-- Header -->
      <header class="header">
        <button class="sidebar-toggle" id="sidebar-toggle">
          <i class="fas fa-bars"></i>
        </button>
        <div class="search-container">
          <i class="fas fa-search search-icon"></i>
          <input type="search" class="search-input" placeholder="Search...">
        </div>
        <div class="header-actions">
          <button class="header-button">
            <i class="fas fa-bell"></i>
          </button>
          <div class="dropdown">
            <button class="header-button dropdown-toggle">
              <i class="fas fa-user"></i>
            </button>
            <div class="dropdown-menu">
              <div class="dropdown-header">My Account</div>
              <div class="dropdown-divider"></div>
              <a href="../dashboard.php" class="dropdown-item">Profile</a>
              <a href="#settings" class="dropdown-item">Settings</a>
              <div class="dropdown-divider"></div>
              <a href="../logout.php" class="dropdown-item">Logout</a>
            </div>
          </div>
        </div>
      </header>

 <!-- Organizations Content -->
  <div class="content hidden" id="organizations-content"> 
    <div class="page-header">
     <h1>Manage Organizations</h1>
     <p class="text-muted">View, add, and manage organization records</p>
  </div>

  <div class="header-actions-container">
    <button class="button" id="add-organization-button">
      <i class="fas fa-building"></i> Add Organization
    </button>
  </div>

  <div class="tabs">
    <div class="tab-content active" id="organizations-tab">
      <div class="card">
        <div class="card-header">
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="search" class="search-input" placeholder="Search organizations..." id="search-organizations">
          </div>
          <div class="button-group">
            <button class="button button-outline">Filter</button>
          </div>
        </div>
        <div class="card-content">
          <div class="table-container">
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Sub-Org</th>
                  <th>Members</th>
                  <th>Category</th>
                  <th>Created</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($organizations as $org): ?>
                <tr>
                  <td><?= htmlspecialchars($org['name']) ?></td>
                  <td><?= htmlspecialchars($org['description']) ?></td>
                  <td><?= htmlspecialchars($org['sub_organization']) ?></td>
                  <td><?= htmlspecialchars($org['members']) ?></td>
                  <td><?= htmlspecialchars($org['category']) ?></td>
                  <td><?= htmlspecialchars($org['created_at']) ?></td>
                  <td class="text-right">
                    <div class="button-group">
                      <button class="icon-button edit-organization" data-id="<?= $org['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button class="icon-button delete-organization" data-id="<?= $org['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Events Content -->
  <div class="content hidden" id="events-content"> 
     <div class="page-header">
      <h1>Manage Events</h1>
     <p class="text-muted">Create, update, and remove events</p>
  </div>

  <div class="header-actions-container">
    <button class="button" id="add-event-button">
      <i class="fas fa-calendar-plus"></i> Add Event
    </button>
  </div>

  <div class="tabs">
    <div class="tab-content active" id="events-tab">
      <div class="card">
        <div class="card-header">
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="search" class="search-input" placeholder="Search events..." id="search-events">
          </div>
          <div class="button-group">
            <button class="button button-outline">Filter</button>
          </div>
        </div>
        <div class="card-content">
          <div class="table-container">
            <table class="table">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Location</th>
                  <th>Org</th>
                  <th>Type</th>
                  <th>Volunteer?</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                  <td><?= htmlspecialchars($event['title']) ?></td>
                  <td><?= htmlspecialchars($event['event_date']) ?></td>
                  <td><?= htmlspecialchars($event['event_time']) ?></td>
                  <td><?= htmlspecialchars($event['location']) ?></td>
                  <td><?= htmlspecialchars($event['organization']) ?></td>
                  <td><?= htmlspecialchars($event['event_type']) ?></td>
                  <td><?= $event['is_volunteer'] ? 'Yes' : 'No' ?></td>
                  <td class="text-right">
                    <div class="button-group">
                      <button class="icon-button edit-event" data-id="<?= $event['id'] ?>"><i class="fas fa-edit"></i></button>
                      <button class="icon-button delete-event" data-id="<?= $event['id'] ?>"><i class="fas fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Dashboard Content -->
      <div class="content" id="dashboard-content">
        <div class="page-header">
          <h1>Dashboard</h1>
          <p class="text-muted">Overview of your platform statistics and performance.</p>
        </div>

        <div class="card-grid">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Total Users</h3>
              <i class="fas fa-users text-muted"></i>
            </div>
            <div class="card-content">
              <div class="card-value"><?= count($allUsers) ?></div>
              <p class="card-trend">+2.5% from last week</p>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Total Events</h3>
              <i class="fas fa-calendar-alt text-muted"></i>
            </div>
            <div class="card-content">
              <div class="card-value">
                <?php
                $stmt = $pdo->prepare("CALL sp_count_events()");
                $stmt->execute();
                $event_count = $stmt->fetchColumn();
                $stmt->closeCursor();
                echo $event_count;
                ?>
              </div>
              <p class="card-trend">+18% from last month</p>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Organizations</h3>
              <i class="fas fa-building text-muted"></i>
            </div>
            <div class="card-content">
              <div class="card-value">
                <?php
                $stmt = $pdo->prepare("CALL sp_count_organizations()");
                $stmt->execute();
                $org_count = $stmt->fetchColumn();
                $stmt->closeCursor();
                echo $org_count;
                ?>
              </div>
              <p class="card-trend">+5% from last month</p>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Active Users</h3>
              <i class="fas fa-user-check text-muted"></i>
            </div>
            <div class="card-content">
              <div class="card-value">78%</div>
              <p class="card-trend">+4% from last week</p>
            </div>
          </div>
        </div>
      </div>


<!-- Users Content -->
      <div class="content hidden" id="users-content">
        <div class="page-header">
          <h1>Manage Users</h1>
          <p class="text-muted">Manage user and admin accounts</p>
        </div>

        <div class="header-actions-container">
          <button class="button" id="add-user-button">
            <i class="fas fa-user-plus"></i> Add User
          </button>
        </div>

        <div class="tabs">
          <div class="tabs-header">
            <button class="tab-button active" data-tab="all-users">All Users</button>
            <button class="tab-button" data-tab="admin-users">Admin Users</button>
            <button class="tab-button" data-tab="regular-users">Regular Users</button>
          </div>

<!-- All Users Tab -->
          <div class="tab-content active" id="all-users-tab">
            <div class="card">
              <div class="card-header">
                <div class="search-container">
                  <i class="fas fa-search search-icon"></i>
                  <input type="search" class="search-input" placeholder="Search users..." id="search-all-users">
                </div>
                <div class="button-group">
                  <button class="button button-outline">Filter</button>
                </div>
              </div>
              <div class="card-content">
                <div class="table-container">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($allUsers as $user): ?>
                      <tr>
                        <td class="font-medium"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                        <td><span class="badge badge-green">Active</span></td>
                        <td class="text-right">
                          <div class="button-group">
                            <button class="icon-button edit-user" data-id="<?php echo $user['id']; ?>"><i class="fas fa-edit"></i></button>
                            <button class="icon-button delete-user" data-id="<?php echo $user['id']; ?>"><i class="fas fa-trash"></i></button>
                          </div>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
     
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="admin.js"></script>
</body>
</html>

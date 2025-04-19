<?php
require_once 'db_connect.php';

$latestEvents = getLatestEvents($pdo, 3);
include 'header.php';
?>

<div class="hero">
  <h1>Community ConnectHub</h1>
  <p>Fostering partnerships and collaboration among university-based organizations.</p>
  <div class="d-flex justify-content-center">
    <a href="events.php" class="btn btn-light mx-2">Explore Events</a>
    <a href="organizations.php" class="btn btn-outline-light mx-2">Discover Organizations</a>
  </div>
</div>

<div class="container my-5">
  <h2 class="text-center">Latest Events</h2>
  <p class="text-center text-muted">Discover upcoming events and activities on campus.</p>
  <div class="row">
    <?php if (!empty($latestEvents)): ?>
      <?php foreach ($latestEvents as $event): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <span class="badge bg-primary mb-2"><?= htmlspecialchars($event['event_type']) ?></span>
              <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
              <p class="text-muted mb-1"><?= date('M d, Y', strtotime($event['event_date'])) ?></p>
              <?php if(isset($event['event_time'])): ?>
                <p class="text-muted"><?= date('h:i A', strtotime($event['event_time'])) ?></p>
              <?php endif; ?>
              <a href="event_details.php?id=<?= $event['event_id'] ?>" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p class="text-muted text-center">No events available at the moment. Check back later!</p>
      </div>
    <?php endif; ?>
  </div>
  <div class="text-center mt-4">
    <a href="events.php" class="btn btn-outline-primary">View All Events</a>
  </div>
</div>

<div class="container my-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h2>Why Join Community ConnectHub?</h2>
      <p class="lead">Community ConnectHub is a comprehensive platform designed to foster collaboration and partnerships among university-based organizations, aligned with SDG 17.</p>
      <div class="d-flex flex-column gap-3 mt-4">
        <div class="d-flex align-items-start">
          <div class="bg-primary text-white rounded-circle p-2 me-3">
            <i class="fas fa-users"></i>
          </div>
          <div>
            <h5>Discover & Connect</h5>
            <p>Find and join organizations that match your interests, connect with like-minded individuals, and build your campus network.</p>
          </div>
        </div>
        <div class="d-flex align-items-start">
          <div class="bg-primary text-white rounded-circle p-2 me-3">
            <i class="fas fa-calendar"></i>
          </div>
          <div>
            <h5>Events & Activities</h5>
            <p>Stay informed about campus events, workshops, and activities. Register, save, and manage your event calendar in one place.</p>
          </div>
        </div>
        <div class="d-flex align-items-start">
          <div class="bg-primary text-white rounded-circle p-2 me-3">
            <i class="fas fa-handshake"></i>
          </div>
          <div>
            <h5>Collaboration Hub</h5>
            <p>Join cross-organizational projects, share resources, and create impact through strategic partnerships.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-body p-4">
          <h4 class="card-title">SDG 17: Partnerships for the Goals</h4>
          <p class="card-text">Community ConnectHub is aligned with Sustainable Development Goal 17, emphasizing meaningful collaboration for sustainable development. By joining, you help drive positive change on campus and beyond.</p>
          <div class="text-center mt-4">
            <a href="register.php" class="btn btn-primary">Join Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

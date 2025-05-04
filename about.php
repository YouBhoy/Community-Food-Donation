<?php
require_once 'db_connect.php';
include 'header.php';
?>

<div class="hero">
  <h1>About ConnectHub</h1>
  <p>Learn more about our mission and vision for university organizations</p>
</div>

<div class="container my-5">
  <div class="row">
    <div class="col-md-8">
      <h2>Our Mission</h2>
      <p class="lead">ConnectHub is a comprehensive platform designed to foster collaboration and partnerships among university-based organizations, aligned with SDG 17: Partnerships for the Goals.</p>
      
      <p>At ConnectHub, we believe that meaningful collaboration is the key to addressing complex challenges. Our platform provides the tools and resources necessary for university organizations to connect, share knowledge, and work together towards common goals.</p>
      
      <h3 class="mt-4">Our Values</h3>
      <div class="row mt-3">
        <div class="col-md-6 mb-3">
          <div class="d-flex">
            <div class="bg-primary text-white rounded-circle p-2 me-3">
              <i class="fas fa-handshake"></i>
            </div>
            <div>
              <h5>Collaboration</h5>
              <p>We believe in the power of working together to achieve greater impact.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="d-flex">
            <div class="bg-primary text-white rounded-circle p-2 me-3">
              <i class="fas fa-globe"></i>
            </div>
            <div>
              <h5>Sustainability</h5>
              <p>We are committed to supporting initiatives that contribute to sustainable development.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="d-flex">
            <div class="bg-primary text-white rounded-circle p-2 me-3">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <h5>Inclusivity</h5>
              <p>We strive to create a platform that is accessible and welcoming to all.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="d-flex">
            <div class="bg-primary text-white rounded-circle p-2 me-3">
              <i class="fas fa-lightbulb"></i>
            </div>
            <div>
              <h5>Innovation</h5>
              <p>We encourage creative solutions and new approaches to collaboration.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card shadow-lg mb-4">
        <div class="card-body">
          <h4>SDG 17: Partnerships for the Goals</h4>
          <p>ConnectHub is aligned with Sustainable Development Goal 17, which emphasizes the importance of partnerships in achieving sustainable development.</p>
          <p>By facilitating collaboration among university organizations, we contribute to the creation of effective partnerships that can drive positive change on campus and beyond.</p>
          <a href="https://sdgs.un.org/goals/goal17" target="_blank" class="btn btn-primary">Learn More About SDG 17</a>
        </div>
      </div>
      
      <div class="card">
        <div class="card-body">
          <h4>Get Involved</h4>
          <p>Ready to join our community? Create an account today to start connecting with organizations and events on campus.</p>
          <a href="register.php" class="btn btn-primary w-100">Join ConnectHub</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

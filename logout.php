<?php
require_once 'config.php';
require_once BASE_PATH.'/includes/auth.php';
logout();

$page_title = 'Logged Out';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
?>

<section class="contact-section spad" style="padding-top:180px; padding-bottom:60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 text-center">
        <div class="section-title contact-title mb-4">
          <span>Goodbye!</span>
          <h2>You have been logged out</h2>
        </div>
        <a href="<?= BASE_URL ?>/login.php" class="primary-btn">Login again</a>
        <a href="<?= BASE_URL ?>/index.php" class="primary-btn" style="background-color:grey;">Back to Home</a>
      </div>
    </div>
  </div>
</section>

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>

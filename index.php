<?php
$page_title = "Home | Fitness Site";
require_once 'config.php';          // DB not used yet, but keep it consistent
require_once 'templates/header.php';
?>

<!-- Hero Section -->
<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container-fluid py-5 text-center">
    <h1 class="display-5 fw-bold">Transform Your Body, Transform Your Life</h1>
    <p class="col-lg-8 mx-auto fs-4">
      Choose a plan, join a class, track your progress—all in one place.
    </p>
    <a href="/pages/plans/list.php" class="btn btn-primary btn-lg">Join Now</a>
  </div>
</div>

<!-- Three-column feature highlights -->
<div class="row text-center">
  <div class="col-md-4">
    <img src="/assets/img/weights.svg" alt="" width="72">
    <h3 class="fw-bold">Flexible Plans</h3>
    <p>Monthly or yearly memberships with student discounts.</p>
  </div>
  <div class="col-md-4">
    <img src="/assets/img/class.svg" alt="" width="72">
    <h3 class="fw-bold">Expert-led Classes</h3>
    <p>Yoga, HIIT, strength training—book your slot instantly.</p>
  </div>
  <div class="col-md-4">
    <img src="/assets/img/stats.svg" alt="" width="72">
    <h3 class="fw-bold">Progress Tracker</h3>
    <p>Log BMI & body-fat%, visualise trends, stay motivated.</p>
  </div>
</div>

<!-- Testimonials Carousel (Bootstrap 5) -->
<div id="testimonialCarousel" class="carousel slide my-5" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active text-center">
      <blockquote class="blockquote">
        <p class="mb-4">“Lost 10 kg and gained confidence thanks to these classes!”</p>
        <footer class="blockquote-footer">Aylin K.</footer>
      </blockquote>
    </div>
    <!-- Add more carousel-item divs later, or query from DB -->
  </div>
</div>

<?php require_once 'templates/footer.php'; ?>

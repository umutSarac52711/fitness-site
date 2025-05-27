<?php
require_once __DIR__ . '/../includes/functions.php';   
$trainers = get_trainers() ?? [];                     
?>

<section class="team-section spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center mb-5">
        <div class="section-title">
          <span>Our Team</span>
          <h2>Certified Trainers</h2>
        </div>
      </div>

      <?php if (!empty($trainers)): ?>
        <?php foreach ($trainers as $tr): ?>
          <?php
            // Determine the image URL for the trainer
            $trainer_image_src = BASE_URL . '/assets/img/default-profile.jpg'; // A sensible default image

            if (!empty($tr['profile_picture'])) {
                $pic_path = ltrim(str_replace('\\\\', '/', $tr['profile_picture']), '/'); // Normalize and remove leading slash

                if (strpos($pic_path, 'http') === 0) { // If it's already a full URL
                    $trainer_image_src = $pic_path;
                } elseif (file_exists(BASE_PATH . '/' . $pic_path)) { // If it's a full relative path from webroot
                    $trainer_image_src = BASE_URL . '/' . $pic_path;
                } elseif (file_exists(BASE_PATH . '/uploads/profile_pictures/' . basename($pic_path))) {
                    // If it's just a filename, check in the standard uploads directory
                    $trainer_image_src = BASE_URL . '/uploads/profile_pictures/' . basename($pic_path);
                } elseif (file_exists(BASE_PATH . '/assets/img/trainers/' . basename($pic_path))) {
                    // Fallback: check in assets/img/trainers/ if it's just a filename
                     $trainer_image_src = BASE_URL . '/assets/img/trainers/' . basename($pic_path);
                }
                // If none of the above, the initial default $trainer_image_src will be used.
            }
          ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 text-center shadow-sm">
              <img src="<?= htmlspecialchars($trainer_image_src) ?>"
                   class="card-img-top" alt="<?= htmlspecialchars($tr['full_name']) ?>" style="height: 300px; object-fit: cover; border-top-left-radius: .25rem; border-top-right-radius: .25rem;">
              <div class="card-body py-3">
                <h5 class="card-title mb-0"><?= htmlspecialchars($tr['full_name']) ?></h5>
                <small class="text-muted">Trainer</small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="col-12 text-center text-muted">No trainers have been added.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

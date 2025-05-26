<?php
require_once __DIR__ . '/../includes/functions.php';   
$trainers = get_trainers() ?? [];                     
?>

<section class="trainer-section spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center mb-5">
        <div class="section-title">
          <span>Our Team</span>
          <h2>Certified Trainers</h2>
        </div>
      </div>

      <?php if ($trainers): ?>
        <?php foreach ($trainers as $tr): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 text-center shadow-sm">
              <img src="<?= BASE_URL ?>/assets/img/trainers/<?= $tr['avatar'] ?: 'default.png' ?>"
                   class="card-img-top" alt="<?= htmlspecialchars($tr['full_name']) ?>">
              <div class="card-body py-3">
                <h5 class="card-title mb-0"><?= htmlspecialchars($tr['full_name']) ?></h5>
                <small class="text-muted">Trainer</small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="col-12 text-center text-muted">Henüz trainer eklenmedi.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

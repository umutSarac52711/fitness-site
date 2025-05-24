<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


$page_title = 'Classes';

$stmt = $pdo->query('SELECT * FROM testimonials ORDER BY id DESC');
$testimonials = $stmt->fetchAll();


require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="main-content container" style="padding-top: 90px; padding-left: auto;">

<div class="main-content container py-4">
  <div class="card shadow-sm mb-4">
    <div class="card-body pb-0">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Testimonials</h1>
        <a href="<?= BASE_URL ?>/pages/testimonials/add.php" class="btn btn-primary">+ New Testimonial</a>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Rating</th>
              <th>Quote</th>
              <th>Created At</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($testimonials as $t): ?>
              <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['name']) ?></td>
                <td><?= htmlspecialchars($t['rating']) ?></td>
                <td><?= htmlspecialchars($t['quote']) ?></td>
                <td><?= htmlspecialchars($t['created_at']) ?></td>
                <td><?= htmlspecialchars($t['status']) ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/pages/testimonials/edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="<?= BASE_URL ?>/pages/testimonials/delete.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this testimonial?');">Del</a>
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
<?php require_once BASE_PATH . '/templates/script.php';?>

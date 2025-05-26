<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home

$page_title = 'Classes';

// Fetch all classes with trainer names
$stmt_classes = $pdo->query("
    SELECT c.id, c.title, c.description, u.full_name AS trainer_name
    FROM classes c
    LEFT JOIN users u ON c.trainer_id = u.id
    ORDER BY c.title ASC");
$classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';

// Retrieve flash message from session if it exists
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container py-4 admin-main-content-block" style="padding-top: 20px;"> <?php // Added admin-main-content-block and adjusted padding ?>
  <div class="card shadow-sm mb-4">
    <div class="card-body pb-0">

    <?php if ($flash_message): ?>
        <div class="alert alert-<?= htmlspecialchars($flash_message['type']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_message['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Classes</h1>
        <a href="<?= BASE_URL ?>/pages/classes/add.php" class="btn btn-primary">+ New Class</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>Title</th>
              <th>Description</th>
              <th>Trainer</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($classes as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['title']) ?></td>
                <td><?= htmlspecialchars(substr($c['description'], 0, 100)) . (strlen($c['description']) > 100 ? '...' : '') ?></td>
                <td><?= htmlspecialchars($c['trainer_name'] ?? 'N/A') ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/pages/classes/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="<?= BASE_URL ?>/pages/classes/delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this class?');">Del</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div style ="margin-top: 60px;">
      <?php require_once BASE_PATH . '/templates/class-timetable-view.php'; ?>
    </div>
  </div>
</div>

</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';?>

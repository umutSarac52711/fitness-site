<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Classes';
$stmt = $pdo->query('SELECT * FROM classes ORDER BY id DESC');
$classes = $stmt->fetchAll();


require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="container py-4">
  <div class="card shadow-sm mb-4">
    <div class="card-body pb-0">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Classes</h1>
        <a href="<?= BASE_URL ?>/pages/classes/add.php" class="btn btn-primary">+ New Class</a>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Room</th>
              <th>Capacity</th>
              <th>Start</th>
              <th>End</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($classes as $c): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['title']) ?></td>
                <td><?= htmlspecialchars($c['room']) ?></td>
                <td><?= htmlspecialchars($c['capacity']) ?></td>
                <td><?= htmlspecialchars($c['start_dt']) ?></td>
                <td><?= htmlspecialchars($c['end_dt']) ?></td>
                <td><?= nl2br(htmlspecialchars($c['description'])) ?></td>
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
  </div>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Progress Logs';

$stmt = $pdo->query('SELECT * FROM progress_logs ORDER BY date DESC, id DESC');
$logs = $stmt->fetchAll();

require_once BASE_PATH . '/templates/header.php';
?>


<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3">Progress Logs</h1>
  <a href="<?= BASE_URL ?>/pages/progress/add.php" class="btn btn-primary">+ New Log</a>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>User ID</th>
      <th>Date</th>
      <th>Weight</th>
      <th>Height</th>
      <th>BMI</th>
      <th>Body Fat</th>
      <th>Notes</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($logs as $log): ?>
      <tr>
        <td><?= $log['id'] ?></td>
        <td><?= $log['user_id'] ?></td>
        <td><?= htmlspecialchars($log['date']) ?></td>
        <td><?= is_null($log['weight']) ? '-' : $log['weight'] ?></td>
        <td><?= is_null($log['height']) ? '-' : $log['height'] ?></td>
        <td><?= is_null($log['bmi']) ? '-' : $log['bmi'] ?></td>
        <td><?= is_null($log['body_fat']) ? '-' : $log['body_fat'] ?></td>
        <td><?= htmlspecialchars($log['notes']) ?></td>
        <td>
          <a href="<?= BASE_URL ?>/pages/progress/edit.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= BASE_URL ?>/pages/progress/delete.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this log?');">Del</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

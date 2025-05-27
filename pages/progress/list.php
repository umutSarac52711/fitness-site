<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Progress Logs';

$stmt = $pdo->query('SELECT * FROM progress_logs ORDER BY date DESC, id DESC');
$logs = $stmt->fetchAll();


require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
    <div class="main-content container admin-main-content-block">

<h1 class="h3 mb-3">Progress Logs</h1>

<?php display_flash_message(); ?>

<div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>User ID</th>
              <th>Date</th>
              <th>Weight</th>
              <th>Height</th>
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
                <td><?= is_null($log['body_fat']) ? '-' : $log['body_fat'] ?></td>
                <td><?= htmlspecialchars($log['notes']) ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/pages/progress/edit.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="<?= BASE_URL ?>/pages/progress/delete.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this log?');">Del</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

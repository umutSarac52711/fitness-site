<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


$page_title = 'Membership Plans';

$stmt = $pdo->query('SELECT * FROM plans ORDER BY id DESC');
$plans = $stmt->fetchAll();

require_once BASE_PATH . '/templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3">Plans</h1>
  <a href="<?= BASE_URL ?>/pages/plans/add.php" class="btn btn-primary">+ New Plan</a>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th><th>Name</th><th>Price</th><th>Weeks</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($plans as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= number_format($p['price'], 2) ?> ₺</td>
        <td><?= $p['duration_weeks'] ?></td>
        <td>
          <a href="<?= BASE_URL ?>/pages/plans/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= BASE_URL ?>/pages/plans/delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this plan?');">Del</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

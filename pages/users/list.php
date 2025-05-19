<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_admin();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = $search ? 'WHERE email LIKE :search' : '';
$sql = "SELECT * FROM users $where ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
if ($search) {
    $stmt->execute([':search' => "%$search%"]);
} else {
    $stmt->execute();
}
$users = $stmt->fetchAll();

$page_title = 'Users';

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="container-fluid" style="padding-top: 90px; background-color: darkgrey; height: 100%;">
  <div class="row">
    <div class="col-12">
      <h1 class="h3">Users</h1>
      <p class="text-muted">Manage users and their roles.</p>
    </div>
  </div>

<div class="container py-4">
  <div class="card shadow-sm mb-4">
    <div class="card-body pb-0">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Users</h1>
        <a href="<?= BASE_URL ?>/pages/users/add.php" class="btn btn-primary">+ New User</a>
      </div>
      <form class="mb-3" method="get">
        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Search by email..." value="<?= htmlspecialchars($search) ?>">
          <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Created At</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr<?= !$u['is_active'] ? ' class="text-muted" style="opacity:.6"' : '' ?>>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['created_at']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td><?= $u['is_active'] ? 'Active' : 'Inactive' ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/pages/users/edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <?php if ($u['is_active']): ?>
                    <a href="<?= BASE_URL ?>/pages/users/delete.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deactivate this user?');">Deactivate</a>
                  <?php endif; ?>
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

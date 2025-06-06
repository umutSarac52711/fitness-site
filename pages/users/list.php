<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_admin();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clauses = [];
$params = [];

if ($search) {
    $search_param = "%$search%";
    $where_clauses[] = '(email LIKE :search OR full_name LIKE :search OR username LIKE :search)';
    $params[':search'] = $search_param;
}

$where = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$sql = "SELECT id, username, full_name, email, created_at, role, is_active FROM users $where ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$page_title = 'Users';

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
<div class="main-content container-fluid">
<div class="container py-4 admin-main-content-block">
  <div class="card shadow-sm mb-4">
    <div class="card-body pb-0">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Users</h1>
        <a href="<?= BASE_URL ?>/pages/users/add.php" class="btn btn-primary">+ New User</a>
      </div>
      <form class="mb-3" method="get">
        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Search by Username, Full Name, Email..." value="<?= htmlspecialchars($search) ?>">
          <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Full Name</th>
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
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['full_name']) ?></td> <!-- Changed from $u['name'] -->
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
</div>
<?php require_once BASE_PATH . '/templates/script.php';?>

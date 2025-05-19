<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid user ID');

$stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) die('User not found');

$self = ($id == $_SESSION['user']['id']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'member';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Prevent admin from demoting themselves
    if ($self && $user['role'] === 'admin' && $role !== 'admin') {
        $error = 'You cannot demote your own admin account.';
    } else {
        try {
            $sql = 'UPDATE users SET name=?, email=?, role=?, is_active=? WHERE id=?';
            $pdo->prepare($sql)->execute([$name, $email, $role, $is_active, $id]);
            header('Location: ' . BASE_URL . '/pages/users/list.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Email address already exists.';
            } else {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}

$page_title = 'Edit User';
require_once BASE_PATH . '/templates/header.php';
?>

<h1 class="h3 mb-4">Edit User #<?= $id ?></h1>

<?php if ($error): ?>
  <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
<?php endif; ?>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input name="name" type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
    <div class="invalid-feedback">Please enter a name.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
    <div class="invalid-feedback">Please enter a valid email.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" class="form-select" <?= $self ? 'disabled' : '' ?>>
      <option value="member"<?= $user['role']==='member'?' selected':'' ?>>Member</option>
      <option value="admin"<?= $user['role']==='admin'?' selected':'' ?>>Admin</option>
    </select>
    <?php if ($self): ?><input type="hidden" name="role" value="admin"><?php endif; ?>
  </div>

  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
      <option value="1"<?= $user['is_active']?' selected':'' ?>>Active</option>
      <option value="0"<?= !$user['is_active']?' selected':'' ?>>Inactive</option>
    </select>
  </div>

  <button class="btn btn-primary">Save Changes</button>
  <a href="<?= BASE_URL ?>/pages/users/list.php" class="btn btn-secondary">Cancel</a>
</form>

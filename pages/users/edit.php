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
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']); // Added username
    $email = trim($_POST['email']);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'member';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Prevent admin from demoting themselves
    if ($self && $user['role'] === 'admin' && $role !== 'admin') {
        $error = 'You cannot demote your own admin account.';
    } elseif (empty($username)) {
        $error = 'Username cannot be empty.';
    } else {
        try {
            // Check if username is being changed and if the new one already exists
            if ($username !== $user['username']) {
                $stmt_check_username = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt_check_username->execute([$username, $id]);
                if ($stmt_check_username->fetch()) {
                    throw new PDOException("Username '{$username}' already exists.", 23000);
                }
            }

            $sql = 'UPDATE users SET full_name=?, username=?, email=?, role=?, is_active=? WHERE id=?'; // Added username=?
            $pdo->prepare($sql)->execute([$full_name, $username, $email, $role, $is_active, $id]);
            header('Location: ' . BASE_URL . '/pages/users/list.php?success=User updated');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                 if (strpos(strtolower($e->getMessage()), 'username') !== false) {
                    $error = "Username '{$username}' already exists.";
                } elseif (strpos(strtolower($e->getMessage()), 'email') !== false) {
                    $error = "Email address '{$email}' already exists for another user.";
                } else {
                    $error = 'A unique field conflict occurred. Please check username and email.';
                }
            } else {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    // If error, update $user array with POSTed values to repopulate form
    if ($error) {
        $user['full_name'] = $full_name;
        $user['username'] = $username;
        $user['email'] = $email;
        $user['role'] = $role;
        $user['is_active'] = $is_active;
    }
}

$page_title = 'Edit User';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="main-content container" style="padding-top: 90px; padding-left: auto;">

<h1 class="h3 mb-4">Edit User #<?= $id ?></h1>

<?php if ($error): ?>
  <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
<?php endif; ?>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input name="full_name" type="text" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
    <div class="invalid-feedback">Please enter a name.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Username</label>
    <input name="username" type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
    <div class="invalid-feedback">Please enter a username.</div>
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

</div>
<?php require_once BASE_PATH . '/templates/script.php';?>
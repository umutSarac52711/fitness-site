<?php
// pages/users/add.php

// 1) Bootstrap the app
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';

// 2) Only admins may use this
require_admin();

// 3) If form posted, validate & insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    // Trim & sanitize inputs
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']); // Added username
    $email    = trim($_POST['email']);
    $password_input = $_POST['password'];
    $role     = $_POST['role'] === 'admin' ? 'admin' : 'member';

    // Basic validation for username (e.g., not empty, maybe more specific rules)
    if (empty($username)) {
        // Handle error - for now, let's assume it's caught by 'required' in HTML
        // Or add specific error handling here
    }

    // 4) Securely hash the password
    $hash = password_hash($password_input, PASSWORD_DEFAULT);

    // 5) Insert into the users table
    $sql = 'INSERT INTO users (full_name, username, email, password, role) 
            VALUES (?, ?, ?, ?, ?)'; // Added username
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$full_name, $username, $email, $hash, $role]);
        // 6) Redirect to avoid form re-submission
        header('Location: ' . BASE_URL . '/pages/users/list.php?success=User added');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation
            // Check if it's a username or email conflict
            if (strpos($e->getMessage(), 'username') !== false) {
                $error = 'Username already exists.';
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                $error = 'Email address already exists.';
            } else {
                $error = 'A unique field conflict occurred. Please check username and email.';
            }
        } else {
            $error = 'Database error: ' . $e->getMessage();
        }
        // If error, fall through to display form again with error message
    }
}

// 7) Page metadata + header
$page_title = 'Add User';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="main-content container" style="padding-top: 90px; padding-left: auto;">

<h1 class="h3 mb-4">Create New User</h1>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input name="full_name" type="text" class="form-control" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
    <div class="invalid-feedback">Please enter a name.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Username</label>
    <input name="username" type="text" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    <div class="invalid-feedback">Please enter a username.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Email address</label>
    <input name="email" type="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    <div class="invalid-feedback">Please enter a valid email.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Password</label>
    <input name="password" type="password" class="form-control" required minlength="6">
    <div class="invalid-feedback">
      Please provide a password (at least 6 characters).
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" class="form-select">
      <option value="member" selected>Member</option>
      <option value="admin">Admin</option>
    </select>
  </div>

  <button type="submit" class="btn btn-success">Create User</button>
  <a href="<?= BASE_URL ?>/pages/users/list.php" class="btn btn-secondary ms-2">Cancel</a>
</form>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

<script>
// Bootstrap client-side validation
(() => {
  'use strict';
  document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
})();
</script>

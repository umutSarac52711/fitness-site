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
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'] === 'admin' ? 'admin' : 'member';

    // 4) Securely hash the password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 5) Insert into the users table
    $sql = 'INSERT INTO users (name, email, password_hash, role)
            VALUES (?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $hash, $role]);

    // 6) Redirect to avoid form re-submission
    header('Location: ' . BASE_URL . '/pages/users/list.php');
    exit;
}

// 7) Page metadata + header
$page_title = 'Add User';
require_once BASE_PATH . '/templates/header.php';
?>

<h1 class="h3 mb-4">Create New User</h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input name="name" type="text" class="form-control" required>
    <div class="invalid-feedback">Please enter a name.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Email address</label>
    <input name="email" type="email" class="form-control" required>
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

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

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

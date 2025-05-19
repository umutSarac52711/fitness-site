<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


$page_title = 'Add Plan';
check_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $price  = (float) $_POST['price'];
    $weeks  = (int)   $_POST['weeks'];
    $active = isset($_POST['active']) ? 1 : 0;

    $sql = 'INSERT INTO plans (name, price, duration_weeks, is_active)
            VALUES (:n, :p, :w, :a)';
    $pdo->prepare($sql)->execute([
        ':n' => $name,
        ':p' => $price,
        ':w' => $weeks,
        ':a' => $active
    ]);

    header('Location: ' . BASE_URL . '/pages/plans/list.php');
    exit;
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<h1 class="h3 mb-3">New Membership Plan</h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Plan name</label>
    <div class="col-sm-10">
      <input name="name" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Price (₺)</label>
    <div class="col-sm-4">
      <input name="price" type="number" step="0.01"
             class="form-control" required>
      <div class="invalid-feedback">Number ≥ 0</div>
    </div>

    <label class="col-sm-2 col-form-label">Weeks</label>
    <div class="col-sm-4">
      <input name="weeks" type="number" min="1"
             class="form-control" required>
    </div>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox"
           name="active" id="active" checked>
    <label class="form-check-label" for="active">Active?</label>
  </div>

  <button class="btn btn-success">Save</button>
  <a href="<?= BASE_URL ?>/pages/plans/list.php"
     class="btn btn-secondary">Cancel</a>
</form>


<?php require_once BASE_PATH . '/templates/script.php';?>

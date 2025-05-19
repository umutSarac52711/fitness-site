<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


check_csrf();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $price  = (float)$_POST['price'];
    $weeks  = (int)  $_POST['weeks'];
    $active = isset($_POST['active']) ? 1 : 0;

    $sql = 'UPDATE plans
              SET name=:n, price=:p, duration_weeks=:w, is_active=:a
            WHERE id=:id';
    $pdo->prepare($sql)->execute([
        ':n'=>$name, ':p'=>$price, ':w'=>$weeks, ':a'=>$active, ':id'=>$id
    ]);

    header('Location: ' . BASE_URL . '/pages/plans/list.php');
    exit;
}

$plan = $pdo->prepare('SELECT * FROM plans WHERE id=?');
$plan->execute([$id]);
$plan = $plan->fetch();
if (!$plan) die('Plan not found');

$page_title = 'Edit Plan';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<h1 class="h3 mb-3">Edit Plan #<?= $id ?></h1>

<form method="POST" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-md-6">
    <label class="form-label">Name</label>
    <input name="name" class="form-control"
           value="<?= htmlspecialchars($plan['name']) ?>" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">Price (₺)</label>
    <input name="price" type="number" step="0.01" class="form-control"
           value="<?= $plan['price'] ?>" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">Duration (weeks)</label>
    <input name="weeks" type="number" class="form-control"
           value="<?= $plan['duration_weeks'] ?>" required>
  </div>
  <div class="col-12">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="active"
             <?= $plan['is_active'] ? 'checked' : '' ?>>
      <label class="form-check-label">Active?</label>
    </div>
  </div>
  <div class="col-12">
    <button class="btn btn-success">Update</button>
    <a href="<?= BASE_URL ?>/pages/plans/list.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>

<?php require_once BASE_PATH . '/templates/script.php';?>

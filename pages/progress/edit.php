<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


check_csrf();


$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $date = $_POST['date'];
    $weight = $_POST['weight'] !== '' ? (float)$_POST['weight'] : null;
    $height = $_POST['height'] !== '' ? (float)$_POST['height'] : null;
    $body_fat = $_POST['body_fat'] !== '' ? (float)$_POST['body_fat'] : null;
    $notes = trim($_POST['notes']);

    $sql = 'UPDATE progress_logs
              SET user_id=:u, date=:d, weight=:w, height=:h, body_fat=:bf, notes=:n
            WHERE id=:id';
    $pdo->prepare($sql)->execute([
        ':u'=>$user_id, ':d'=>$date, ':w'=>$weight, ':h'=>$height, ':bf'=>$body_fat, ':n'=>$notes, ':id'=>$id
    ]);

    header('Location: ' . BASE_URL . '/pages/progress/list.php');
    exit;
}

$log = $pdo->prepare('SELECT * FROM progress_logs WHERE id=?');
$log->execute([$id]);
$log = $log->fetch();
if (!$log) die('Progress log not found');

$page_title = 'Edit Progress Log';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
    <div class="main-content container admin-main-content-block">

<h1 class="h3 mb-3">Edit Progress Log #<?= $id ?></h1>

<p class="text-danger">Please ensure the data is correct before submitting.</p>
<form method="POST" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-md-4">
    <label class="form-label">User ID</label>
    <input name="user_id" type="number" class="form-control" value="<?= htmlspecialchars($log['user_id']) ?>" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Date</label>
    <input name="date" type="date" class="form-control" value="<?= htmlspecialchars($log['date']) ?>" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Weight (kg)</label>
    <input name="weight" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($log['weight']) ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">Height (cm)</label>
    <input name="height" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($log['height']) ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">Body Fat (%)</label>
    <input name="body_fat" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($log['body_fat']) ?>">
  </div>
  <div class="col-md-12">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($log['notes']) ?></textarea>
  </div>
  <div class="col-12">
    <button class="btn btn-success">Update</button>
    <a href="<?= BASE_URL ?>/pages/progress/list.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>

    </div>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

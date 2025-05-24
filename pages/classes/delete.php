<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pdo->prepare('DELETE FROM classes WHERE id=?')->execute([$id]);
    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

$page_title = 'Delete Class';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="main-content container" style="padding-top: 90px; padding-left: auto;">

<h1 class="h4">Delete Class #<?= $id ?>?</h1>
<p class="text-danger">This action cannot be undone.</p>

<form method="POST">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn btn-danger">Yes, delete</button>
  <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

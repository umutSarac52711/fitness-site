<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid user ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pdo->prepare('UPDATE users SET is_active=0 WHERE id=?')->execute([$id]);
    header('Location: ' . BASE_URL . '/pages/users/list.php');
    exit;
}

$page_title = 'Deactivate User';
require_once BASE_PATH . '/templates/header.php';
?>

<h1 class="h4">Deactivate User #<?= $id ?>?</h1>
<p class="text-danger">This will deactivate the user but not delete their data. Continue?</p>

<form method="POST">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn btn-danger">Yes, deactivate</button>
  <a href="<?= BASE_URL ?>/pages/users/list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

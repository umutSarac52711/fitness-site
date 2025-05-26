<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid post ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pdo->prepare('DELETE FROM posts WHERE id=?')->execute([$id]);
    header('Location: ' . BASE_URL . '/pages/posts/list.php');
    exit;
}

$page_title = 'Delete Post';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container admin-main-content-block" style="padding-top: 20px; padding-left: auto;"> <?php // Applied dark content block and adjusted padding ?>

<h1 class="h4">Delete Post #<?= $id ?>?</h1>
<p class="text-danger">This action cannot be undone.</p>

<form method="POST">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn btn-danger">Yes, delete</button>
  <a href="<?= BASE_URL ?>/pages/posts/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';?>
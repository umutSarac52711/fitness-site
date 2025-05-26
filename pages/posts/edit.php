<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();

check_csrf();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid post ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_id = (int)$_POST['author_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $created_at = $_POST['created_at'] ?: date('Y-m-d H:i:s');
    $updated_at = $_POST['updated_at'] ?: null;

    $sql = 'UPDATE posts SET author_id=:author_id, title=:title, content=:content, created_at=:created_at, updated_at=:updated_at WHERE id=:id';
    $pdo->prepare($sql)->execute([
        ':author_id' => $author_id,
        ':title' => $title,
        ':content' => $content,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at,
        ':id' => $id
    ]);

    header('Location: ' . BASE_URL . '/pages/posts/list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id=?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) die('Post not found');

$page_title = 'Edit Post';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container admin-main-content-block" style="padding-top: 20px; padding-left: auto;"> <?php // Applied dark content block and adjusted padding ?>

<h1 class="h3 mb-3">Edit Post #<?= $id ?></h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Author ID</label>
    <div class="col-sm-4">
      <input name="author_id" type="number" class="form-control" value="<?= htmlspecialchars($post['author_id']) ?>" required>
      <div class="invalid-feedback">Required</div>
    </div>
    <label class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-4">
      <input name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Content</label>
    <div class="col-sm-10">
      <textarea name="content" class="form-control" rows="5"><?= htmlspecialchars($post['content']) ?></textarea>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Created At</label>
    <div class="col-sm-4">
      <input name="created_at" type="datetime-local" class="form-control" value="<?= str_replace(' ', 'T', $post['created_at']) ?>">
    </div>
    <label class="col-sm-2 col-form-label">Updated At</label>
    <div class="col-sm-4">
      <input name="updated_at" type="datetime-local" class="form-control" value="<?= str_replace(' ', 'T', $post['updated_at']) ?>">
    </div>
  </div>

  <button class="btn btn-primary">Save Changes</button>
  <a href="<?= BASE_URL ?>/pages/posts/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';?>

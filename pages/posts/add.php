<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();

$page_title = 'Add Post';
check_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_id = (int)$_POST['author_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $created_at = $_POST['created_at'] ?: date('Y-m-d H:i:s');
    $updated_at = $_POST['updated_at'] ?: null;

    $sql = 'INSERT INTO posts (author_id, title, content, created_at, updated_at) VALUES (:author_id, :title, :content, :created_at, :updated_at)';
    $pdo->prepare($sql)->execute([
        ':author_id' => $author_id,
        ':title' => $title,
        ':content' => $content,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at
    ]);

    header('Location: ' . BASE_URL . '/pages/posts/list.php');
    exit;
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<h1 class="h3 mb-3">New Post</h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Author ID</label>
    <div class="col-sm-4">
      <input name="author_id" type="number" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>
    <label class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-4">
      <input name="title" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Content</label>
    <div class="col-sm-10">
      <textarea name="content" class="form-control" rows="5"></textarea>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Created At</label>
    <div class="col-sm-4">
      <input name="created_at" type="datetime-local" class="form-control">
    </div>
    <label class="col-sm-2 col-form-label">Updated At</label>
    <div class="col-sm-4">
      <input name="updated_at" type="datetime-local" class="form-control">
    </div>
  </div>

  <button class="btn btn-primary">Add Post</button>
  <a href="<?= BASE_URL ?>/pages/posts/list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once BASE_PATH . '/templates/script.php';?>
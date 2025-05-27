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
    $updated_at = date('Y-m-d H:i:s'); // Automatically set updated_at to current time

    // Fetch existing post data to preserve created_at and old cover_img if no new one is uploaded
    $stmt_old_post = $pdo->prepare("SELECT created_at, cover_img FROM posts WHERE id = :id");
    $stmt_old_post->execute([':id' => $id]);
    $old_post_data = $stmt_old_post->fetch(PDO::FETCH_ASSOC);
    $created_at = $old_post_data ? $old_post_data['created_at'] : date('Y-m-d H:i:s');
    $cover_img_path_db = $old_post_data ? $old_post_data['cover_img'] : null; // Keep old image by default

    // Handle file upload for cover_img (if a new one is provided)
    if (isset($_FILES['cover_img']) && $_FILES['cover_img']['error'] == UPLOAD_ERR_OK && !empty($_FILES['cover_img']['tmp_name'])) {
        $upload_dir = rtrim(BASE_PATH, '/') . '/uploads/blog_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $original_filename = basename($_FILES['cover_img']['name']);
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $new_filename = 'post_' . uniqid('', true) . '.' . $imageFileType;
        $target_file = $upload_dir . $new_filename;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if ($_FILES['cover_img']['size'] < 5000000) { // Max 5MB
                if (move_uploaded_file($_FILES['cover_img']['tmp_name'], $target_file)) {
                    // Delete old image if a new one is successfully uploaded and an old one exists
                    if ($cover_img_path_db && file_exists(rtrim(BASE_PATH, '/') . $cover_img_path_db)) {
                        unlink(rtrim(BASE_PATH, '/') . $cover_img_path_db);
                    }
                    $cover_img_path_db = '/uploads/blog_images/' . $new_filename; // Relative path for DB
                } else {
                    // Optional: set_flash_message('Error moving new uploaded file.', 'danger');
                }
            } else {
                // Optional: set_flash_message('New file is too large. Max 5MB allowed.', 'danger');
            }
        } else {
            // Optional: set_flash_message('Invalid new file type. Allowed: JPG, JPEG, PNG, GIF.', 'danger');
        }
    }

    $sql = 'UPDATE posts SET author_id=:author_id, title=:title, content=:content, created_at=:created_at, updated_at=:updated_at, cover_img=:cover_img WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':author_id' => $author_id,
        ':title' => $title,
        ':content' => $content,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at,
        ':cover_img' => $cover_img_path_db,
        ':id' => $id
    ]);

    header('Location: ' . BASE_URL . '/pages/posts/list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id=?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) die('Post not found');

// Fetch authors for the dropdown
$authors_stmt = $pdo->query("SELECT id, username, full_name FROM users ORDER BY full_name, username");
$authors = $authors_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Edit Post';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container admin-main-content-block" style="padding-top: 20px; padding-left: auto;"> <?php // Applied dark content block and adjusted padding ?>

<h1 class="h3 mb-3">Edit Post #<?= $id ?></h1>

<form method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Author</label>
    <div class="col-sm-4">
      <select name="author_id" class="form-select" required>
        <option value="">Select Author...</option>
        <?php foreach ($authors as $author): ?>
            <?php 
                $display_name = !empty($author['full_name']) ? $author['full_name'] : $author['username'];
                $selected = ($author['id'] == $post['author_id']) ? 'selected' : '';
            ?>
            <option value="<?= htmlspecialchars($author['id']) ?>" <?= $selected ?>>
                <?= htmlspecialchars($author['id']) ?> - <?= htmlspecialchars($display_name) ?>
            </option>
        <?php endforeach; ?>
      </select>
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
    <label for="cover_img" class="col-sm-2 col-form-label">Cover Image</label>
    <div class="col-sm-10">
      <input type="file" name="cover_img" id="cover_img" class="form-control">
      <small class="form-text text-muted">Optional. Upload a new image to replace the current one. Allowed types: JPG, JPEG, PNG, GIF. Max size: 5MB.</small>
      <?php if (!empty($post['cover_img'])): ?>
        <div class="mt-2">
          <p class="mb-1">Current image:</p>
          <img src="<?= BASE_URL . htmlspecialchars($post['cover_img']) ?>" alt="Current Cover Image" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
        </div>
      <?php endif; ?>
    </div>
  </div>
  <button class="btn btn-primary">Save Changes</button>
  <a href="<?= BASE_URL ?>/pages/posts/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';

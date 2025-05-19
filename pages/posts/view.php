<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) die('Invalid post ID');

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) die('Post not found');

$page_title = 'View Post';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="container mt-4">
  <h1 class="h3 mb-3">Post Details</h1>
  <table class="table table-bordered w-auto bg-white">
    <tr>
      <th>Author ID</th>
      <td><?= htmlspecialchars($post['author_id']) ?></td>
    </tr>
    <tr>
      <th>Title</th>
      <td><?= htmlspecialchars($post['title']) ?></td>
    </tr>
    <tr>
      <th>Content</th>
      <td><?= nl2br(htmlspecialchars($post['content'])) ?></td>
    </tr>
    <tr>
      <th>Created At</th>
      <td><?= htmlspecialchars($post['created_at']) ?></td>
    </tr>
    <tr>
      <th>Updated At</th>
      <td><?= htmlspecialchars($post['updated_at']) ?></td>
    </tr>
  </table>
  <a href="<?= BASE_URL ?>/pages/posts/list.php" class="btn btn-secondary">Back to Posts</a>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>
<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Posts';
$stmt = $pdo->query('SELECT * FROM posts ORDER BY id DESC');
$posts = $stmt->fetchAll();

require_once BASE_PATH . '/templates/header.php';
?>


<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3">Posts</h1>
  <a href="<?= BASE_URL ?>/pages/posts/add.php" class="btn btn-primary">+ New Post</a>
</div>


<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Author ID</th>
      <th>Title</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($posts as $post): ?>
      <tr>
        <td><?= $post['id'] ?></td>
        <td><?= htmlspecialchars($post['author_id']) ?></td>
        <td><?= htmlspecialchars($post['title']) ?></td>
        <td><?= htmlspecialchars($post['created_at']) ?></td>
        <td><?= htmlspecialchars($post['updated_at']) ?></td>
        <td>
          <a href="<?= BASE_URL ?>/pages/posts/view.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-info">View</a>
          <a href="<?= BASE_URL ?>/pages/posts/edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= BASE_URL ?>/pages/posts/delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this post?');">Del</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

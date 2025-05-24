<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Posts';
$stmt = $pdo->query('SELECT * FROM posts ORDER BY id DESC');
$posts = $stmt->fetchAll();


require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="main-content container" style="padding-top: 90px; padding-left: auto;">
<div class="main-content container py-4">
  <div class="card shadow-sm mb-4">
    <div class="card-body pb-0">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Posts</h1>
        <a href="<?= BASE_URL ?>/pages/posts/add.php" class="btn btn-primary">+ New Post</a>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
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
                  <a href="<?= BASE_URL ?>/pages/posts/delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?');">Del</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

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
    $rating = $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $quote = trim($_POST['quote']);
    $status = $_POST['status'] ?? null;
    $created_at = $_POST['created_at'] !== '' ? $_POST['created_at'] : null;

    $sql = 'UPDATE testimonials
              SET user_id=:user_id, rating=:rating, quote=:quote, created_at=:created_at, status=:status
            WHERE id=:id';
    $pdo->prepare($sql)->execute([
        ':user_id'=>$user_id,
        ':rating'=>$rating,
        ':quote'=>$quote,
        ':created_at'=>$created_at,
        ':status'=>$status,
        ':id'=>$id
    ]);

    header('Location: ' . BASE_URL . '/pages/testimonials/list.php');
    exit;
}

$testimonial = $pdo->prepare('SELECT * FROM testimonials WHERE id=?');
$testimonial->execute([$id]);
$testimonial = $testimonial->fetch();
if (!$testimonial) die('Testimonial not found');

$page_title = 'Edit Testimonial';
require_once BASE_PATH . '/templates/header.php';
?>


<h1 class="h3 mb-3">Edit Testimonial #<?= $id ?></h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">User ID</label>
    <div class="col-sm-4">
      <input name="user_id" type="number" class="form-control" value="<?= htmlspecialchars($testimonial['user_id']) ?>" required>
      <div class="invalid-feedback">Required</div>
    </div>

    <label class="col-sm-2 col-form-label">Rating</label>
    <div class="col-sm-4">
      <input name="rating" type="number" min="1" max="5" class="form-control" value="<?= htmlspecialchars($testimonial['rating']) ?>">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Quote</label>
    <div class="col-sm-10">
      <input name="quote" class="form-control" value="<?= htmlspecialchars($testimonial['quote']) ?>">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Created At</label>
    <div class="col-sm-4">
      <input name="created_at" type="datetime-local" class="form-control" value="<?= htmlspecialchars($testimonial['created_at']) ?>">
      <div class="form-text">Leave blank for current time</div>
    </div>

    <label class="col-sm-2 col-form-label">Status</label>
    <div class="col-sm-4">
      <select name="status" class="form-select">
        <option value="">-- Select --</option>
        <option value="pending" <?= $testimonial['status']==='pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $testimonial['status']==='approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $testimonial['status']==='rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>
  </div>

  <button class="btn btn-success">Update</button>
  <a href="<?= BASE_URL ?>/pages/testimonials/list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Add Testimonial';
check_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $rating = $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $quote = trim($_POST['quote']);
    $status = $_POST['status'] ?? null;
    $created_at = DateTime::createFromFormat('Y-m-d', $_POST['created_at']);

    $sql = 'INSERT INTO testimonials (name, rating, quote, created_at, status)
            VALUES (:name, :rating, :quote, :created_at, :status)';
    $pdo->prepare($sql)->execute([
        ':name' => $name,
        ':rating' => $rating,
        ':quote' => $quote,
        ':created_at' => $created_at,
        ':status' => $status
    ]);

    header('Location: ' . BASE_URL . '/pages/testimonials/list.php');
    exit;
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
<div class="admin-main-content-block" style="padding: 20px;">

<h1 class="h3 mb-3">New Testimonial</h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Full Name</label>
    <div class="col-sm-4">
      <input name="name" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>

    <label class="col-sm-2 col-form-label">Rating</label>
    <div class="col-sm-4">
      <input name="rating" type="number" min="1" max="5" class="form-control">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Quote</label>
    <div class="col-sm-10">
      <input name="quote" class="form-control">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Created At</label>
    <div class="col-sm-4">
      <input name="created_at" type="datetime-local" class="form-control">
      <div class="form-text">Leave blank for current time</div>
    </div>

    <label class="col-sm-2 col-form-label">Status</label>
    <div class="col-sm-4">
      <select name="status" class="form-select">
        <option value="">-- Select --</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>
  </div>

  <button class="btn btn-success">Save</button>
  <a href="<?= BASE_URL ?>/pages/testimonials/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>
<?php require_once BASE_PATH . '/templates/script.php';?>

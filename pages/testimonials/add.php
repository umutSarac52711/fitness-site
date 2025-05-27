<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Add Testimonial';
check_csrf();

// Removed user fetching for dropdown

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); // Changed from user_id back to name
    $rating = $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $quote = trim($_POST['quote']);
    $status = $_POST['status'] ?? null;
    // Automatically set created_at timestamp
    $created_at = date('Y-m-d H:i:s');
    // Handle file upload for testimonial photo
    $photo_path_db = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = rtrim(BASE_PATH, '/') . '/uploads/testimonial_photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $original_name = basename($_FILES['photo']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed) && $_FILES['photo']['size'] <= 5000000) {
            $new_filename = 'testimonial_' . uniqid('', true) . '.' . $ext;
            $target = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photo_path_db = '/uploads/testimonial_photos/' . $new_filename;
            }
        }
    }

    $sql = 'INSERT INTO testimonials (name, rating, quote, status, photo, created_at)
            VALUES (:name, :rating, :quote, :status, :photo, :created_at)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name, // Changed from user_id back to name
        ':rating' => $rating,
        ':quote' => $quote,
        ':status' => $status,
        ':photo' => $photo_path_db,
        ':created_at' => $created_at
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

<form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label for="name" class="col-sm-2 col-form-label">Full Name</label>
    <div class="col-sm-4">
      <input type="text" name="name" id="name" class="form-control" required>
      <div class="invalid-feedback">Full Name is required.</div>
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
    <label class="col-sm-2 col-form-label">Photo</label>
    <div class="col-sm-10">
      <input name="photo" type="file" class="form-control" accept=".jpg,.jpeg,.png,.gif">
    </div>
  </div>
  <div class="row mb-3">
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

<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


check_csrf();

 $id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid ID');
// Fetch existing photo path for deletion handling
$old_photo = null;
$photoStmt = $pdo->prepare('SELECT photo FROM testimonials WHERE id = ?');
$photoStmt->execute([$id]);
$photoRow = $photoStmt->fetch(PDO::FETCH_ASSOC);
if ($photoRow) {
    $old_photo = $photoRow['photo'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); // Changed from user_id to name
    $rating = $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $quote = trim($_POST['quote']);
    $status = $_POST['status'] ?? null;
    // Handle file upload for photo
    $photo_path_db = $old_photo;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = rtrim(BASE_PATH, '/') . '/uploads/testimonial_photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $orig = basename($_FILES['photo']['name']);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed) && $_FILES['photo']['size'] <= 5000000) {
            $newName = 'testimonial_' . uniqid('', true) . '.' . $ext;
            $dest = $upload_dir . $newName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $photo_path_db = '/uploads/testimonial_photos/' . $newName;
                // Delete old photo if exists
                if (!empty($old_photo)) {
                    $oldPath = rtrim(BASE_PATH, '/') . $old_photo;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }
        }
    }
    // Update testimonial record, removing manual created_at
    $sql = 'UPDATE testimonials
              SET name=:name, rating=:rating, quote=:quote, status=:status, photo=:photo
            WHERE id=:id'; // Changed user_id to name
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name, // Changed from user_id
        ':rating' => $rating,
        ':quote' => $quote,
        ':status' => $status,
        ':photo' => $photo_path_db,
        ':id' => $id
    ]);

    header('Location: ' . BASE_URL . '/pages/testimonials/list.php');
    exit;
}

$testimonial_stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id=?'); // Renamed variable to avoid conflict
$testimonial_stmt->execute([$id]);
$testimonial = $testimonial_stmt->fetch();
if (!$testimonial) die('Testimonial not found');

$page_title = 'Edit Testimonial';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
<div class="admin-main-content-block" style="padding: 20px;">

<h1 class="h3 mb-3">Edit Testimonial #<?= $id ?></h1>

<form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label for="name" class="col-sm-2 col-form-label">Full Name</label>
    <div class="col-sm-4">
      <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($testimonial['name']) ?>" required>
      <div class="invalid-feedback">Full Name is required.</div>
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

  <!-- Photo upload -->
  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Photo</label>
    <div class="col-sm-10">
      <input name="photo" type="file" class="form-control" accept=".jpg,.jpeg,.png,.gif">
      <?php if (!empty($testimonial['photo'])): ?>
        <img src="<?= BASE_URL . htmlspecialchars($testimonial['photo']) ?>" alt="Current photo" class="img-thumbnail mt-2" style="max-height: 120px;">
      <?php endif; ?>
    </div>
  </div>
  <!-- Status select -->
  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Status</label>
    <div class="col-sm-4">
      <select name="status" class="form-select">
        <option value="">-- Select --</option>
        <option value="pending" <?= $testimonial['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $testimonial['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $testimonial['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>
  </div>

  <button class="btn btn-success">Update</button>
  <a href="<?= BASE_URL ?>/pages/testimonials/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>
<?php require_once BASE_PATH . '/templates/script.php';?>

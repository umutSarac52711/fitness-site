<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home


check_csrf();


$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $room = trim($_POST['room']);
    $capacity = $_POST['capacity'] !== '' ? (int)$_POST['capacity'] : null;
    $start_dt = $_POST['start_dt'];
    $end_dt = $_POST['end_dt'];
    $description = trim($_POST['description']);

    $sql = 'UPDATE classes SET title=:title, room=:room, capacity=:capacity, start_dt=:start_dt, end_dt=:end_dt, description=:description WHERE id=:id';
    $pdo->prepare($sql)->execute([
        ':title' => $title,
        ':room' => $room,
        ':capacity' => $capacity,
        ':start_dt' => $start_dt,
        ':end_dt' => $end_dt,
        ':description' => $description,
        ':id' => $id
    ]);

    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

$class = $pdo->prepare('SELECT * FROM classes WHERE id=?');
$class->execute([$id]);
$class = $class->fetch();
if (!$class) die('Class not found');

$page_title = 'Edit Class';
require_once BASE_PATH . '/templates/header.php';
?>

<h1 class="h3 mb-3">Edit Class #<?= $id ?></h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-10">
      <input name="title" class="form-control" value="<?= htmlspecialchars($class['title']) ?>" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Room</label>
    <div class="col-sm-4">
      <input name="room" class="form-control" value="<?= htmlspecialchars($class['room']) ?>">
    </div>
    <label class="col-sm-2 col-form-label">Capacity</label>
    <div class="col-sm-4">
      <input name="capacity" type="number" class="form-control" value="<?= htmlspecialchars($class['capacity']) ?>">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Start Date/Time</label>
    <div class="col-sm-4">
      <input name="start_dt" type="datetime-local" class="form-control" value="<?= str_replace(' ', 'T', $class['start_dt']) ?>" required>
      <div class="invalid-feedback">Required</div>
    </div>
    <label class="col-sm-2 col-form-label">End Date/Time</label>
    <div class="col-sm-4">
      <input name="end_dt" type="datetime-local" class="form-control" value="<?= str_replace(' ', 'T', $class['end_dt']) ?>" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Description</label>
    <div class="col-sm-10">
      <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($class['description']) ?></textarea>
    </div>
  </div>

  <button class="btn btn-primary">Save Changes</button>
  <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn btn-secondary">Cancel</a>
</form>
    <input name="price" type="number" step="0.01" class="form-control"
           value="<?= $plan['price'] ?>" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">Duration (weeks)</label>
    <input name="weeks" type="number" class="form-control"
           value="<?= $plan['duration_weeks'] ?>" required>
  </div>
  <div class="col-12">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="active"
             <?= $plan['is_active'] ? 'checked' : '' ?>>
      <label class="form-check-label">Active?</label>
    </div>
  </div>
  <div class="col-12">
    <button class="btn btn-success">Update</button>
    <a href="<?= BASE_URL ?>/pages/plans/list.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>

<?php require_once BASE_PATH . '/templates/footer.php'; ?>

<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home



$page_title = 'Add Class';
check_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $room = trim($_POST['room']);
    $capacity = $_POST['capacity'] !== '' ? (int)$_POST['capacity'] : null;
    $start_dt = $_POST['start_dt'];
    $end_dt = $_POST['end_dt'];
    $description = trim($_POST['description']);
    $trainerUID = $_POST['trainerUID'];
    if (!$trainerUID) {
        $trainerUID = 1;
    }

    $sql = 'INSERT INTO classes (title, room, capacity, start_dt, end_dt, description, trainer_id)
            VALUES (:title, :room, :capacity, :start_dt, :end_dt, :description, :trainerUID)';
    $pdo->prepare($sql)->execute([
        ':title' => $title,
        ':room' => $room,
        ':capacity' => $capacity,
        ':start_dt' => $start_dt,
        ':end_dt' => $end_dt,
        ':description' => $description,
        ':trainerUID' => $trainerUID
    ]);

    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>


<div class="main-content container" style="padding-top: 90px; padding-left: auto;">

<h1 class="h3 mb-3">New Class</h1>

<form method="POST" class="needs-validation" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-10">
      <input name="title" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Room</label>
    <div class="col-sm-4">
      <input name="room" class="form-control">
    </div>
    <label class="col-sm-2 col-form-label">Capacity</label>
    <div class="col-sm-4">
      <input name="capacity" type="number" class="form-control">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Start Date/Time</label>
    <div class="col-sm-4">
      <input name="start_dt" type="datetime-local" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>
    <label class="col-sm-2 col-form-label">End Date/Time</label>
    <div class="col-sm-4">
      <input name="end_dt" type="datetime-local" class="form-control" required>
      <div class="invalid-feedback">Required</div>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Description</label>
    <div class="col-sm-10">
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-2 col-form-label">TrainerUID</label>
    <div class="col-sm-4">
      <input name="trainerUID" class="form-control">
    </div>
  </div>

  <button class="btn btn-primary">Add Class</button>
  <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

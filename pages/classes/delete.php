<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home

// Start session if not already started, for flash messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['flash_messages'] = [['type' => 'danger', 'message' => 'Invalid class ID.']];
    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

// Fetch class name for a more informative message (optional, but good UX)
$stmt_class = $pdo->prepare("SELECT title FROM classes WHERE id = ?");
$stmt_class->execute([$id]);
$class = $stmt_class->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    $_SESSION['flash_messages'] = [['type' => 'danger', 'message' => 'Class not found.']];
    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        $pdo->beginTransaction();

        // Delete associated class timetable entries
        $stmt_delete_timetable = $pdo->prepare('DELETE FROM class_timetable WHERE class_id = ?');
        $stmt_delete_timetable->execute([$id]);

        // Delete the class
        $stmt_delete_class = $pdo->prepare('DELETE FROM classes WHERE id = ?');
        $stmt_delete_class->execute([$id]);

        $pdo->commit();
        $_SESSION['flash_messages'] = [['type' => 'success', 'message' => 'Class and its schedule deleted successfully.']];
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error deleting class: " . $e->getMessage());
        $_SESSION['flash_messages'] = [['type' => 'danger', 'message' => 'Failed to delete class. Please try again.']];
    }
    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

$page_title = 'Delete Class';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container admin-main-content-block" style="padding-top: 20px; padding-left: auto;"> <?php // Added admin-main-content-block and adjusted padding ?>

<h1 class="h4">Delete Class: <?= htmlspecialchars($class['title'] ?? 'ID #'.$id) ?>?</h1>
<p class="text-danger">This action will also delete all associated schedule entries and cannot be undone.</p>

<form method="POST" action="<?= htmlspecialchars(BASE_URL . '/pages/classes/delete.php?id=' . $id) ?>">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <button type="submit" class="btn btn-danger">Yes, delete</button>
  <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';?>

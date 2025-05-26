<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Invalid user ID.'];
    header('Location: ' . BASE_URL . '/pages/users/list.php');
    exit;
}

// Fetch user details for display or confirmation
$stmt_check = $pdo->prepare("SELECT full_name, role FROM users WHERE id = ?");
$stmt_check->execute([$id]);
$user_to_delete = $stmt_check->fetch();

if (!$user_to_delete) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'User not found.'];
    header('Location: ' . BASE_URL . '/pages/users/list.php');
    exit;
}

$is_trainer = ($user_to_delete['role'] === 'trainer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    try {
        $pdo->beginTransaction();

        if ($is_trainer) {
            // If the user is a trainer, we need to handle their classes.
            // Option 1: Delete classes and their timetable entries.
            // Option 2: Set trainer_id to NULL for their classes (if schema allows).
            // Option 3: Prevent deletion if they have classes (current approach here is to delete).

            // Find all classes taught by this trainer
            $stmt_find_classes = $pdo->prepare("SELECT id FROM classes WHERE trainer_id = ?");
            $stmt_find_classes->execute([$id]);
            $trainer_class_ids = $stmt_find_classes->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($trainer_class_ids)) {
                // Delete from class_timetable for each class
                $placeholders = implode(',', array_fill(0, count($trainer_class_ids), '?'));
                $stmt_delete_timetables = $pdo->prepare("DELETE FROM class_timetable WHERE class_id IN (" . $placeholders . ")");
                $stmt_delete_timetables->execute($trainer_class_ids);

                // Delete the classes themselves
                $stmt_delete_classes = $pdo->prepare("DELETE FROM classes WHERE trainer_id = ?");
                $stmt_delete_classes->execute([$id]);
            }
        }

        // Deactivate or Delete the user.
        // Current file from context suggests deactivation (is_active = 0).
        // If actual deletion is required, change to: DELETE FROM users WHERE id = ?
        $stmt_user = $pdo->prepare('UPDATE users SET is_active=0 WHERE id=?'); // As per original file context
        // To actually delete: $stmt_user = $pdo->prepare('DELETE FROM users WHERE id=?');
        $stmt_user->execute([$id]);

        $pdo->commit();
        $action_taken = $is_trainer ? "User deactivated, and their classes/schedules deleted." : "User deactivated successfully.";
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => $action_taken];
        header('Location: ' . BASE_URL . '/pages/users/list.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error processing user: " . $e->getMessage());
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Could not process user due to a database error.'];
        header('Location: ' . BASE_URL . '/pages/users/delete.php?id=' . $id);
        exit;
    }
}

$page_title = 'Deactivate User';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
<div class="admin-main-content-block" style="padding: 20px;">

<h1 class="h4">Deactivate User: "<?= htmlspecialchars($user_to_delete['full_name']) ?>"?</h1>
<?php if ($is_trainer): ?>
<p class="text-danger">This user is a trainer. Deactivating them will also <strong>permanently delete all classes they teach and their schedules</strong>. This action cannot be undone.</p>
<?php else: ?>
<p class="text-warning">This will deactivate the user but not delete their data. Continue?</p>
<?php endif; ?>

<form method="POST" action="<?= htmlspecialchars(BASE_URL . '/pages/users/delete.php?id=' . $id) ?>">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <button type="submit" class="btn btn-danger">Yes, deactivate</button>
</form>
</div>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>

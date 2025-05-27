<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php'; // For CSRF, flash messages
require_once BASE_PATH . '/includes/auth.php';

require_login(); // Ensure user is logged in

$log_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];

if (!$log_id) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid progress log ID for deletion.'];
    header('Location: ' . BASE_URL . '/pages/auth/account.php#progress');
    exit;
}

// Optional: CSRF check for GET requests if you deem it necessary, though typically for state-changing POST.
// For a delete link, a CSRF token in the URL is one way, or a confirmation form POST.
// For simplicity here, we'll proceed if the log belongs to the user.

// Fetch the log to ensure it belongs to the current user before deleting
$stmt_check = $pdo->prepare("SELECT id FROM progress_logs WHERE id = ? AND user_id = ?");
$stmt_check->execute([$log_id, $user_id]);
$log_exists = $stmt_check->fetch();

if (!$log_exists) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Progress log not found or you do not have permission to delete it.'];
    header('Location: ' . BASE_URL . '/pages/auth/account.php#progress');
    exit;
}

// If we are here, the log exists and belongs to the user. Proceed with deletion.
// A more robust system might use a POST request for deletion with CSRF token.
// The onclick confirm in the link provides a basic user confirmation.

try {
    $sql_delete = "DELETE FROM progress_logs WHERE id = ? AND user_id = ?";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([$log_id, $user_id]);

    if ($stmt_delete->rowCount() > 0) {
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Progress log deleted successfully.'];
    } else {
        // This case should ideally not be reached if the check above passed and no race condition occurred.
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Could not delete the progress log. It might have already been removed.'];
    }
} catch (PDOException $e) {
    error_log("Error deleting progress log $log_id for user $user_id: " . $e->getMessage());
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Failed to delete progress log due to a database error.'];
}

header('Location: ' . BASE_URL . '/pages/auth/account.php#progress');
exit;
?>

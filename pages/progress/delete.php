<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    set_flash_message('Invalid progress log ID.', 'danger');
    header('Location: ' . BASE_PATH . '/pages/progress/list.php');
    exit;
}

// Fetch progress log details for confirmation message
$stmt = $pdo->prepare('SELECT user_id, date FROM progress_logs WHERE id = ?');
$stmt->execute([$id]);
$log = $stmt->fetch();

if (!$log) {
    set_flash_message('Progress log not found.', 'danger');
    header('Location: ' . BASE_URL . '/pages/progress/list.php');
    exit;
}
$user_id_display = $log['user_id'];
$log_date_display = $log['date'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    // Corrected table name
    $stmt = $pdo->prepare('DELETE FROM progress_logs WHERE id = ?');
    if ($stmt->execute([$id])) {
        set_flash_message('Progress log deleted successfully.', 'success');
    } else {
        set_flash_message('Failed to delete progress log.', 'danger');
    }
    // Corrected redirect URL
    header('Location: ' . BASE_URL . '/pages/progress/list.php');
    exit;
}

$page_title = 'Delete Progress Log'; // Corrected page title
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>
<div class="admin-content-area-wrapper">
    <div class="main-content container mt-5 admin-main-content-block">
        <h1 class="h3 mb-3"><?php echo htmlspecialchars($page_title); ?></h1>
        <?php display_flash_message(); ?>
        <p>Are you sure you want to delete this progress record for user ID: <strong><?php echo htmlspecialchars($user_id_display); ?></strong> logged on <strong><?php echo htmlspecialchars($log_date_display); ?></strong>?</p>
        <form method="POST">
            <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($_SESSION['_csrf_token']); ?>">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/templates/script.php'; ?>

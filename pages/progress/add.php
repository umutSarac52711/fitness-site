<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

require_admin(); // kicks non-admins to home

$page_title = 'Add Progress Log';
$error_message = '';
// $success_message = ''; // Flash messages are used for success

// Fetch users for dropdown
$users = [];
try {
    $stmt_users = $pdo->query("SELECT id, username, full_name FROM users ORDER BY username ASC");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching users for progress log form: " . $e->getMessage());
    $error_message = "Could not load users list.";
    // Consider how to handle this - perhaps disable form or show error prominently
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $user_id = (int)($_POST['user_id'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
    $height = !empty($_POST['height']) ? (float)$_POST['height'] : null;
    // BMI is not directly submitted or stored in the database table per user clarification
    $body_fat = !empty($_POST['body_fat']) ? (float)$_POST['body_fat'] : null;
    $notes = trim($_POST['notes'] ?? '');

    if (empty($user_id) || $user_id <= 0) {
        $error_message = "A valid user must be selected.";
    } elseif (empty($date)) {
        $error_message = "Date is required.";
    } else {
        // Basic date validation (can be enhanced)
        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            $error_message = "Invalid date format. Please use YYYY-MM-DD.";
        } else {
            try {
                $sql = 'INSERT INTO progress_logs (user_id, date, weight, height, body_fat, notes)
                        VALUES (:user_id, :date, :weight, :height, :body_fat, :notes)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':date' => $date,
                    ':weight' => $weight,
                    ':height' => $height,
                    ':body_fat' => $body_fat,
                    ':notes' => $notes
                ]);

                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Progress log added successfully for user ID ' . $user_id . '.'];
                header('Location: ' . BASE_URL . '/pages/progress/list.php');
                exit;
            } catch (PDOException $e) {
                error_log("Error adding progress log via admin for user $user_id: " . $e->getMessage());
                $error_message = "Database error: Could not add progress log. Details: " . $e->getMessage();
            }
        }
    }
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';
?>

<div class="admin-content-area-wrapper">
    <div class="main-content container admin-main-content-block">
        <h1 class="h3 mb-3"><?= htmlspecialchars($page_title) ?></h1>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php display_flash_message(); ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

            <div class="row mb-3">
                <label for="user_id" class="col-sm-3 col-form-label">User <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option value="">Select User...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>" <?= (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username'] . ($user['full_name'] ? ' (' . $user['full_name'] . ')' : '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">A user must be selected.</div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="date" class="col-sm-3 col-form-label">Date <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input name="date" id="date" type="date" class="form-control" value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>" required>
                    <div class="invalid-feedback">Date is required.</div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="weight" class="col-sm-3 col-form-label">Weight (kg)</label>
                <div class="col-sm-9">
                    <input name="weight" id="weight" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($_POST['weight'] ?? '') ?>" placeholder="e.g., 70.5">
                </div>
            </div>

            <div class="row mb-3">
                <label for="height" class="col-sm-3 col-form-label">Height (cm)</label>
                <div class="col-sm-9">
                    <input name="height" id="height" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($_POST['height'] ?? '') ?>" placeholder="e.g., 175">
                </div>
            </div>

            <div class="row mb-3">
                <label for="body_fat" class="col-sm-3 col-form-label">Body Fat (%)</label>
                <div class="col-sm-9">
                    <input name="body_fat" id="body_fat" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($_POST['body_fat'] ?? '') ?>" placeholder="e.g., 15.2">
                </div>
            </div>

            <div class="row mb-3">
                <label for="notes" class="col-sm-3 col-form-label">Notes</label>
                <div class="col-sm-9">
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes about the progress..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-success">Add Progress Log</button>
                    <a href="<?= BASE_URL ?>/pages/progress/list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/templates/script.php';?>
<?php require_once BASE_PATH . '/templates/footer.php';?>

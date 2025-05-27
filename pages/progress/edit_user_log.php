<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

require_login(); // Ensure user is logged in

$log_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];
$error_message = '';
$success_message = '';

if (!$log_id) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid progress log ID.'];
    header('Location: ' . BASE_URL . '/pages/auth/account.php#progress');
    exit;
}

// Fetch the log to ensure it belongs to the current user
$stmt_check = $pdo->prepare("SELECT * FROM progress_logs WHERE id = ? AND user_id = ?");
$stmt_check->execute([$log_id, $user_id]);
$log_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$log_data) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Progress log not found or you do not have permission to edit it.'];
    header('Location: ' . BASE_URL . '/pages/auth/account.php#progress');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); // Assuming you have a CSRF check function

    $date = trim($_POST['log_date']);
    $weight = !empty($_POST['log_weight']) ? (float)$_POST['log_weight'] : null;
    $height = !empty($_POST['log_height']) ? (float)$_POST['log_height'] : null;
    $bmi = !empty($_POST['log_bmi']) ? (float)$_POST['log_bmi'] : null;
    $body_fat = !empty($_POST['log_body_fat']) ? (float)$_POST['log_body_fat'] : null;
    $notes = trim($_POST['log_notes']);

    if (empty($date)) {
        $error_message = "Date is required.";
    } else {
        // Recalculate BMI if height and weight are provided and BMI is not
        if (empty($bmi) && !empty($weight) && !empty($height) && $height > 0) {
            $height_m = $height / 100; // Convert cm to meters
            $bmi = round($weight / ($height_m * $height_m), 2);
        }

        try {
            $sql_update = 'UPDATE progress_logs 
                           SET date = ?, weight = ?, height = ?, bmi = ?, body_fat = ?, notes = ? 
                           WHERE id = ? AND user_id = ?';
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$date, $weight, $height, $bmi, $body_fat, $notes, $log_id, $user_id]);
            
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Progress log updated successfully!'];
            header('Location: ' . BASE_URL . '/pages/auth/account.php#progress');
            exit;
        } catch (PDOException $e) {
            error_log("Error updating progress log $log_id for user $user_id: " . $e->getMessage());
            $error_message = "Failed to update progress log: " . $e->getMessage();
        }
    }
}

$page_title = "Edit My Progress Log";
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><?= htmlspecialchars($page_title) ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?id=<?= $log_id ?>#progress">
                        <input type="hidden" name="csrf" value="<?= csrf_token() // Generate CSRF token ?>">
                        
                        <div class="mb-3">
                            <label for="log_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="log_date" name="log_date" value="<?= htmlspecialchars($log_data['date'] ?? date('Y-m-d')) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="log_weight" class="form-label">Weight (kg)</label>
                                <input type="number" step="0.01" class="form-control" id="log_weight" name="log_weight" value="<?= htmlspecialchars($log_data['weight'] ?? '') ?>" placeholder="e.g., 70.5">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="log_height" class="form-label">Height (cm)</label>
                                <input type="number" step="0.01" class="form-control" id="log_height" name="log_height" value="<?= htmlspecialchars($log_data['height'] ?? '') ?>" placeholder="e.g., 175">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="log_bmi" class="form-label">BMI</label>
                                <input type="number" step="0.01" class="form-control" id="log_bmi" name="log_bmi" value="<?= htmlspecialchars($log_data['bmi'] ?? '') ?>" placeholder="Auto-calculated if empty">
                                <small class="form-text text-muted">If empty & weight/height provided, BMI is calculated.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="log_body_fat" class="form-label">Body Fat (%)</label>
                                <input type="number" step="0.01" class="form-control" id="log_body_fat" name="log_body_fat" value="<?= htmlspecialchars($log_data['body_fat'] ?? '') ?>" placeholder="e.g., 15.2">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="log_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="log_notes" name="log_notes" rows="3" placeholder="Optional notes..."><?= htmlspecialchars($log_data['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="<?= BASE_URL ?>/pages/auth/account.php#progress" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once BASE_PATH . '/templates/script.php'; // For JS if any specific to template
require_once BASE_PATH . '/templates/footer.php'; 
?>

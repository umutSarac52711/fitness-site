<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';

require_once BASE_PATH . '/includes/auth.php';
require_admin();   // kicks non-admins to home

// Fetch trainers for the dropdown
$stmt_trainers = $pdo->query("SELECT id, full_name FROM users WHERE role = 'trainer' ORDER BY full_name");
$trainers = $stmt_trainers->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Add New Class'; // Changed page title slightly
$errors = []; 
$form_data = $_POST; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); 

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $trainer_id = $_POST['trainer_id'] ?? '';
    // Expecting an array of selected timeslots
    $selected_timeslots = $_POST['selected_timeslots'] ?? []; 

    // Basic validation
    if (empty($title)) {
        $errors['title'] = 'Title is required.';
    }
    if (empty($trainer_id)) {
        $errors['trainer_id'] = 'Trainer is required.';
    }
    // Validate that at least one timeslot is selected
    if (empty($selected_timeslots) || !is_array($selected_timeslots)) {
        $errors['selected_timeslots'] = 'Please select at least one timeslot for the class.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $sql_class = "INSERT INTO classes (title, description, trainer_id) VALUES (?, ?, ?)";
            $stmt_class = $pdo->prepare($sql_class);
            $stmt_class->execute([$title, $description, $trainer_id]);
            $class_id = $pdo->lastInsertId();

            // Insert each selected timeslot into class_timetable
            $sql_timetable = "INSERT INTO class_timetable (class_id, day_of_week, time_of_day) VALUES (?, ?, ?)";
            $stmt_timetable = $pdo->prepare($sql_timetable);

            foreach ($selected_timeslots as $timeslot_value) { // Renamed $timeslot to $timeslot_value to avoid conflict
                list($day_of_week, $time_of_day) = explode('|', $timeslot_value, 2);
                // Ensure day_of_week is lowercase before inserting, to match ENUM definition
                $stmt_timetable->execute([$class_id, strtolower($day_of_week), $time_of_day]);
            }

            $pdo->commit(); 

            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Class added and scheduled successfully for all selected timeslots!'];
            header('Location: ' . BASE_URL . '/pages/classes/list.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack(); 
            error_log("Error adding class or scheduling: " . $e->getMessage());
            // Provide a more specific error if it's a duplicate entry for timetable
            if ($e->getCode() == '23000') { // Integrity constraint violation
                 $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Failed to add class. One or more selected timeslots might already be booked or a database error occurred.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Failed to add class or schedule due to a database error. Please try again.'];
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("General error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'An unexpected error occurred. Please try again.'];
        }
    }
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';

// Retrieve flash messages for display
$flash_message_display = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

// $form_errors are just $errors from the POST block if validation failed
$form_errors = $errors; 

?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container py-5 admin-main-content-block" style="padding-top: 20px !important;"> <?php // Added admin-main-content-block and adjusted padding ?>
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0 py-2 text-center"><?= htmlspecialchars($page_title) ?></h1>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php
                    if ($flash_message_display) {
                        echo '<div class="alert alert-' . htmlspecialchars($flash_message_display['type']) . ' alert-dismissible fade show" role="alert">';
                        echo htmlspecialchars($flash_message_display['message']);
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                        echo '</div>';
                    }
                    ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                        <fieldset class="mb-4">
                            <legend class="h5 mb-3 border-bottom pb-2">Class Details</legend>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">Class Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($form_errors['title']) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= htmlspecialchars($form_data['title'] ?? '') ?>" required>
                                    <?php if (isset($form_errors['title'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($form_errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="trainer_id" class="form-label">Trainer <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($form_errors['trainer_id']) ? 'is-invalid' : '' ?>" id="trainer_id" name="trainer_id" required>
                                        <option value="">Select Trainer</option>
                                        <?php foreach ($trainers as $trainer): ?>
                                            <option value="<?= htmlspecialchars($trainer['id']) ?>" <?= (isset($form_data['trainer_id']) && $form_data['trainer_id'] == $trainer['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($trainer['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($form_errors['trainer_id'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($form_errors['trainer_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label">Capacity</label>
                                    <input type="number" class="form-control <?= isset($form_errors['capacity']) ? 'is-invalid' : '' ?>" id="capacity" name="capacity" value="<?= htmlspecialchars($form_data['capacity'] ?? '') ?>" min="0">
                                    <?php if (isset($form_errors['capacity'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($form_errors['capacity']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($form_data['description'] ?? '') ?></textarea>
                            </div>
                        </fieldset>

                        <fieldset class="mb-4">
                            <legend class="h5 mb-3 border-bottom pb-2">Schedule Class <span class="text-danger">*</span></legend>
                            <?php
                            include BASE_PATH . '/templates/class-timetable-add.php';
                            ?>
                        </fieldset>
                        
                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg">Add Class and Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';?>

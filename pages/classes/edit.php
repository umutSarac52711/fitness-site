<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

require_admin(); // Kicks non-admins to home

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Invalid Class ID.'];
    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

// Fetch trainers for the dropdown
$stmt_trainers = $pdo->query("SELECT id, full_name FROM users WHERE role = 'trainer' ORDER BY full_name");
$trainers = $stmt_trainers->fetchAll(PDO::FETCH_ASSOC);

// Fetch the class to edit
$stmt_class = $pdo->prepare('SELECT * FROM classes WHERE id = ?');
$stmt_class->execute([$id]);
$class = $stmt_class->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Class not found.'];
    header('Location: ' . BASE_URL . '/pages/classes/list.php');
    exit;
}

// For schedule editing - fetch current class schedule
$stmt_current_schedule = $pdo->prepare("SELECT day_of_week, time_of_day FROM class_timetable WHERE class_id = :class_id");
$stmt_current_schedule->bindParam(':class_id', $id, PDO::PARAM_INT);
$stmt_current_schedule->execute();
$current_schedule_slots_db = $stmt_current_schedule->fetchAll(PDO::FETCH_ASSOC);

$selected_timeslots_from_db = [];
foreach ($current_schedule_slots_db as $slot) {
    // Format as "day|time" for consistency with the timetable template and POST data
    $selected_timeslots_from_db[] = strtolower($slot['day_of_week']) . '|' . $slot['time_of_day'];
}

// This will hold the timeslots to be pre-selected in the form.
// If POST data exists (e.g., after a failed validation), use that. Otherwise, use DB data.
$selected_timeslots_for_form = $_POST['selected_timeslots'] ?? $selected_timeslots_from_db;

$page_title = 'Edit Class';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); // Check CSRF token

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $trainer_id = $_POST['trainer_id'] ?? '';
    $posted_selected_timeslots = $_POST['selected_timeslots'] ?? []; // Array of "day|time"

    // Update $selected_timeslots_for_form with the latest POST data for repopulation on error
    $selected_timeslots_for_form = $posted_selected_timeslots;

    // Basic validation
    if (empty($title)) {
        $errors['title'] = 'Title is required.';
    }
    if (empty($description)) {
        $errors['description'] = 'Description is required.';
    }
    if (empty($trainer_id)) {
        $errors['trainer_id'] = 'Trainer is required.';
    }
    if (empty($posted_selected_timeslots)) {
        $errors['selected_timeslots'] = 'At least one time slot must be selected for the class.';
    }

    // Validate selected timeslots (ensure they are not already booked by *another* class)
    if (!empty($posted_selected_timeslots)) {
        $placeholders = implode(',', array_fill(0, count($posted_selected_timeslots), '(?, ?)'));
        $values_to_check = [];
        foreach ($posted_selected_timeslots as $slot) {
            list($day, $time) = explode('|', $slot);
            $values_to_check[] = strtolower($day); // Ensure day is lowercase for DB check
            $values_to_check[] = $time;
        }

        // Exclude slots currently booked by *this* class from the check
        // Use only positional placeholders '?'
        $sql_check_slots = "SELECT COUNT(*) FROM class_timetable WHERE class_id != ? AND (day_of_week, time_of_day) IN (VALUES " . $placeholders . ")";
        $stmt_check_slots = $pdo->prepare($sql_check_slots);
        
        // Combine $id with $values_to_check for execute()
        $all_params_for_check_slots = array_merge([$id], $values_to_check);
        $stmt_check_slots->execute($all_params_for_check_slots);

        if ($stmt_check_slots->fetchColumn() > 0) {
            $errors['selected_timeslots'] = "One or more selected time slots are already booked by another class. Please refresh and try again.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Update classes table
            $sql_class_update = "UPDATE classes SET title = :title, description = :description, trainer_id = :trainer_id WHERE id = :id";
            $stmt_class_update = $pdo->prepare($sql_class_update);
            $stmt_class_update->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt_class_update->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt_class_update->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt_class_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_class_update->execute();

            // --- Manage class_timetable entries ---
            // 1. Identify slots to delete: slots in $selected_timeslots_from_db but not in $posted_selected_timeslots
            $slots_to_delete = array_diff($selected_timeslots_from_db, $posted_selected_timeslots);
            if (!empty($slots_to_delete)) {
                $sql_delete_timetable = "DELETE FROM class_timetable WHERE class_id = :class_id AND day_of_week = :day_of_week AND time_of_day = :time_of_day";
                $stmt_delete_timetable = $pdo->prepare($sql_delete_timetable);
                foreach ($slots_to_delete as $slot_to_delete_value) {
                    list($day_del, $time_del) = explode('|', $slot_to_delete_value);
                    $day_del_lower = strtolower($day_del); // Store in variable
                    $stmt_delete_timetable->bindParam(':class_id', $id, PDO::PARAM_INT);
                    $stmt_delete_timetable->bindParam(':day_of_week', $day_del_lower, PDO::PARAM_STR);
                    $stmt_delete_timetable->bindParam(':time_of_day', $time_del, PDO::PARAM_STR);
                    $stmt_delete_timetable->execute();
                }
            }

            // 2. Identify slots to add: slots in $posted_selected_timeslots but not in $selected_timeslots_from_db
            $slots_to_add = array_diff($posted_selected_timeslots, $selected_timeslots_from_db);
            if (!empty($slots_to_add)) {
                $sql_insert_timetable = "INSERT INTO class_timetable (class_id, day_of_week, time_of_day) VALUES (:class_id, :day_of_week, :time_of_day)";
                $stmt_insert_timetable = $pdo->prepare($sql_insert_timetable);
                foreach ($slots_to_add as $slot_to_add_value) {
                    list($day_add, $time_add) = explode('|', $slot_to_add_value);
                    $day_add_lower = strtolower($day_add); // Store in variable
                    $stmt_insert_timetable->bindParam(':class_id', $id, PDO::PARAM_INT);
                    $stmt_insert_timetable->bindParam(':day_of_week', $day_add_lower, PDO::PARAM_STR); // Ensure lowercase
                    $stmt_insert_timetable->bindParam(':time_of_day', $time_add, PDO::PARAM_STR);
                    $stmt_insert_timetable->execute();
                }
            }

            $pdo->commit();
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Class updated successfully!'];
            header('Location: ' . BASE_URL . '/pages/classes/list.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error updating class: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Could not update class due to a database error. ' . $e->getMessage()];
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("General error updating class: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'An unexpected error occurred. ' . $e->getMessage()];
        }
    } else {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => implode("<br>", $errors)];
    }
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header-admin.php';

// Retrieve flash message from session if it exists
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

?>
<div class="admin-content-area-wrapper"> <?php // Admin background wrapper ?>
<div class="main-content container admin-main-content-block" style="padding-top: 20px; padding-left: auto;"> <?php // Added admin-main-content-block and adjusted padding ?>

<h1 class="h3 mb-3">Edit Class #<?= htmlspecialchars($class['id']) ?></h1>

<?php if ($flash_message): ?>
    <div class="alert alert-<?= htmlspecialchars($flash_message['type']) ?>">
        <?= htmlspecialchars($flash_message['message']) ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/pages/classes/edit.php?id=<?= htmlspecialchars($class['id']) ?>" class="needs-validation" novalidate>
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="title" class="form-label">Class Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $class['title']) ?>" required>
                <?php if (isset($errors['title'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="trainer_id" class="form-label">Trainer <span class="text-danger">*</span></label>
                <select class="form-select <?= isset($errors['trainer_id']) ? 'is-invalid' : '' ?>" id="trainer_id" name="trainer_id" required>
                    <option value="">Select Trainer</option>
                    <?php foreach ($trainers as $trainer): ?>
                        <option value="<?= htmlspecialchars($trainer['id']) ?>" <?= (($POST['trainer_id'] ?? $class['trainer_id']) == $trainer['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($trainer['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['trainer_id'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['trainer_id']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="room" class="form-label">Room</label>
                <input type="text" class="form-control" id="room" name="room" value="<?= htmlspecialchars($_POST['room'] ?? $class['room']) ?>">
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>" id="capacity" name="capacity" value="<?= htmlspecialchars($_POST['capacity'] ?? $class['capacity']) ?>" min="0">
                <?php if (isset($errors['capacity'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['capacity']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? $class['description']) ?></textarea>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Update Class Schedule <span class="text-danger">*</span>
        </div>
        <div class="card-body">
            <p class="text-muted small">Select one or more time slots for this class. Slots booked by <em>other</em> classes are shown as text. This class's current slots are pre-selected.</p>
            <?php
            // Pass $selected_timeslots_for_form for repopulation and pre-selection.
            // The class-timetable-add.php will use the existing $pdo connection.
            // It also needs $current_class_id to correctly disable slots booked by *other* classes.
            $_POST_backup_for_template = $_POST; // Backup global $_POST
            $_POST['selected_timeslots'] = $selected_timeslots_for_form;
            $current_class_id_for_template = $id; // Pass current class ID to template
            include '../../templates/class-timetable-add.php';
            $_POST = $_POST_backup_for_template; // Restore global $_POST
            unset($current_class_id_for_template); // Clean up
            ?>
        </div>
    </div>

    <p class="mt-4"><em>Note: To change the schedule for this class (day/time), you currently need to delete this class and add it again with the new schedule. Advanced schedule editing is not yet implemented.</em></p>

    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
    <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn btn-secondary mt-3">Cancel</a>
</form>

</div>
</div> <?php // End admin background wrapper ?>

<?php require_once BASE_PATH . '/templates/script.php';

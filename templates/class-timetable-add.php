<?php
// This template is used for both adding a new class (selecting available slots)
// and editing an existing class (showing its current slots and allowing changes).

// Define time slots and days for the timetable structure
$time_slots = [
    "6.00am - 8.00am",
    "10.00am - 12.00am",
    "5.00pm - 7.00pm",
    "7.00pm - 9.00pm"
];
// These are for display in table headers. For keys, we'll use lowercase.
$days_of_week_display = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

// This variable will be populated from the including script (add.php or edit.php)
// It contains an array of strings like "monday|10.00am - 12.00am" for pre-selected slots.
$pre_selected_slots = $_POST['selected_timeslots'] ?? []; // Relies on add.php/edit.php setting this in $_POST context

// For edit.php, this ID is passed to identify the class being edited.
// This helps in determining which slots are booked by *other* classes vs. the current class.
$current_editing_class_id = $current_class_id_for_template ?? null; // Relies on edit.php setting this variable

$scheduled_classes_for_add = []; // Initialize an empty array to hold data about booked slots

// Database query to fetch currently scheduled classes to mark them as booked
try {
    if (!isset($pdo)) {
        // Fallback to ensure $pdo is available, similar to class-timetable-view.php
        $config_path_local = __DIR__ . '/../../config.php';
        $config_path_pages = __DIR__ . '/../config.php';
        if (file_exists($config_path_local)) {
            require_once $config_path_local;
        } elseif (file_exists($config_path_pages)) {
            require_once $config_path_pages;
        }
        else {
            throw new Exception("config.php not found. PDO object cannot be initialized.");
        }
    }

    // Fetch all scheduled items along with their class_id
    $stmt_booked = $pdo->query("
        SELECT ct.day_of_week, ct.time_of_day, c.title AS class_title, ct.class_id
        FROM class_timetable ct
        JOIN classes c ON ct.class_id = c.id
    ");

    if ($stmt_booked) {
        while ($row = $stmt_booked->fetch(PDO::FETCH_ASSOC)) {
            $day_key = strtolower($row['day_of_week']); // Use lowercase day for consistent key usage
            $time_key = $row['time_of_day'];
            if (!isset($scheduled_classes_for_add[$day_key])) {
                $scheduled_classes_for_add[$day_key] = [];
            }
            $scheduled_classes_for_add[$day_key][$time_key] = [
                'title' => $row['class_title'],
                'class_id' => $row['class_id'] // Store class_id to compare with $current_editing_class_id
            ];
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching timetable data for class-timetable-add.php: " . $e->getMessage());
    echo "<p class=\"text-danger\">Could not load timetable slot data due to a database error.</p>";
} catch (Exception $e) {
    error_log("General error in class-timetable-add.php: " . $e->getMessage());
    echo "<p class=\"text-danger\">Could not load timetable slot data: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>

<div class="class-timetable-add table-responsive">
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th class="class-time">Time</th>
                <?php foreach ($days_of_week_display as $day_header): ?>
                    <th><?= htmlspecialchars($day_header) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($time_slots as $time_slot_value): ?>
                <tr>
                    <td class="class-time align-middle"><b><?= htmlspecialchars($time_slot_value) ?></b></td>
                    <?php foreach ($days_of_week_display as $day_header_for_key): ?>
                        <?php
                            $current_day_key = strtolower($day_header_for_key); // Lowercase for array key
                            $slot_value_attr = htmlspecialchars($current_day_key . '|' . $time_slot_value);
                            $is_checked = in_array($current_day_key . '|' . $time_slot_value, $pre_selected_slots);
                            $is_disabled = false;
                            $slot_content = '';

                            if (isset($scheduled_classes_for_add[$current_day_key][$time_slot_value])) {
                                $booking_info = $scheduled_classes_for_add[$current_day_key][$time_slot_value];
                                // If we are editing a class, and this slot is booked by the class being edited,
                                // it should be selectable (checked if it was previously selected).
                                // If it's booked by *another* class, it should be disabled.
                                if ($current_editing_class_id !== null && $booking_info['class_id'] == $current_editing_class_id) {
                                    // This slot is booked by the current class being edited. It should be selectable.
                                    // $is_checked is already determined above based on $pre_selected_slots.
                                } else {
                                    // This slot is booked by another class (or we are on add.php and it's booked).
                                    $is_disabled = true;
                                    $slot_content = "<div class=\"booked-slot-text\"><small>Booked:<br>" . htmlspecialchars($booking_info['title']) . "</small></div>";
                                }
                            }
                        ?>
                        <td class="align-middle <?= $is_disabled ? 'slot-disabled' : '' ?> <?= $is_checked ? 'slot-selected-current' : '' ?>">
                            <?php if ($is_disabled): ?>
                                <?= $slot_content ?>
                            <?php else: ?>
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input timetable-checkbox" type="checkbox"
                                           name="selected_timeslots[]"
                                           value="<?= $slot_value_attr ?>"
                                           id="slot_<?= htmlspecialchars($current_day_key . '_' . str_replace(' ', '', $time_slot_value)) ?>"
                                           <?= $is_checked ? 'checked' : '' ?>
                                           <?= $is_disabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label sr-only" for="slot_<?= htmlspecialchars($current_day_key . '_' . str_replace(' ', '', $time_slot_value)) ?>">
                                        Select <?= htmlspecialchars($day_header_for_key) ?> at <?= htmlspecialchars($time_slot_value) ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<style>
    .class-timetable-add .slot-disabled {
        background-color: #f8f9fa; /* Light grey for disabled */
        color: #6c757d; /* Muted text color */
        font-style: italic;
        vertical-align: middle;
    }
    .class-timetable-add .booked-slot-text {
        font-size: 0.8rem;
        line-height: 1.2;
    }
    .class-timetable-add .timetable-checkbox {
        transform: scale(1.3);
        cursor: pointer;
    }
    .class-timetable-add td.slot-selected-current {
        background-color: #e6f7ff; /* Example: light blue for currently selected slots */
    }
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }
</style>
<?php

// Define time slots and days for the timetable structure
$time_slots = [
    "6.00am - 8.00am",
    "10.00am - 12.00am",
    "5.00pm - 7.00pm",
    "7.00pm - 9.00pm"
];
$days_of_week_display = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

$scheduled_classes = []; // Initialize an empty array to hold class data

// Database query to fetch timetable data
try {
    if (!isset($pdo)) {
        $config_path_local = __DIR__ . '/../../config.php';
        $config_path_pages = __DIR__ . '/../config.php';
        $config_path_static = __DIR__ . '/../../../config.php';

        if (file_exists($config_path_local)) {
            require_once $config_path_local;
        } elseif (file_exists($config_path_pages)) {
            require_once $config_path_pages;
        } elseif (file_exists($config_path_static)) {
            require_once $config_path_static;
        }
        else {
            throw new Exception("config.php not found. PDO object cannot be initialized. Ensure \$pdo is available.");
        }
    }

    $stmt = $pdo->query("
        SELECT
            c.title AS class_title,
            ct.day_of_week,
            ct.time_of_day,
            COALESCE(u.full_name, 'N/A') AS trainer_name
        FROM class_timetable ct
        JOIN classes c ON ct.class_id = c.id
        LEFT JOIN users u ON c.trainer_id = u.id
    ");

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $day_key = strtolower($row['day_of_week']);
            $time_key = $row['time_of_day'];
            if (!isset($scheduled_classes[$day_key])) {
                $scheduled_classes[$day_key] = [];
            }
            $scheduled_classes[$day_key][$time_key] = [
                'title' => $row['class_title'],
                'trainer' => $row['trainer_name']
            ];
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching timetable data for class-timetable-view.php: " . $e->getMessage());
    echo "<p class=\"text-danger\">Could not load timetable data due to a database error.</p>";
} catch (Exception $e) {
    error_log("General error in class-timetable-view.php: " . $e->getMessage());
    echo "<p class=\"text-danger\">Could not load timetable data: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>

<div class="row">
    <div class="col-lg-12">
        <div class="class-timetable details-timetable table-responsive"> 
            <table class="table table-bordered text-center"> 
                <thead>
                    <tr>
                        <th></th>
                        <?php foreach ($days_of_week_display as $day_header): ?>
                            <th><?= htmlspecialchars($day_header) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($time_slots as $time_idx => $time_slot_value): ?>
                        <tr>
                            <td class="class-time"><?= htmlspecialchars($time_slot_value) ?></td>
                            <?php foreach ($days_of_week_display as $day_idx => $day_header_for_key): ?>
                                <?php
                                    $current_day_key = strtolower($day_header_for_key);
                                    $cell_classes = [];
                                    // Logic for dark-bg: apply if time_idx and day_idx are both even or both odd
                                    if (($time_idx % 2) == ($day_idx % 2)) {
                                        $cell_classes[] = 'dark-bg';
                                    }

                                    if (isset($scheduled_classes[$current_day_key][$time_slot_value])): 
                                        $class_info = $scheduled_classes[$current_day_key][$time_slot_value];
                                        $cell_classes[] = 'hover-dp'; // Class from SQLrequest for booked cells
                                        $cell_classes[] = 'ts-meta';  // Class from SQLrequest for booked cells
                                ?>
                                    <td class="<?= implode(' ', $cell_classes) ?>">
                                        <h5><?= htmlspecialchars($class_info['title']) ?></h5>
                                        <span><?= htmlspecialchars($class_info['trainer']) ?></span>
                                    </td>
                                <?php else: 
                                    $cell_classes[] = 'blank-td'; // Class from SQLrequest for empty cells
                                ?>
                                    <td class="<?= implode(' ', $cell_classes) ?>">
                                        &nbsp;
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
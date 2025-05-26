<?php
$page_title = "Class Timetable";
require_once '../../config.php';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';


?>

    <!-- Class Timetable Section Begin -->
    <section class="class-timetable-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title">
                        <span>All Classes</span>
                        <h2>Find Your Time</h2>
                    </div>
                </div>
                <!-- Add filter mechanics If you CARE -->
                <!-- Category-based filters removed as category field is removed
                <div class="col-lg-6">
                    <div class="table-controls">
                        <ul>
                            <li class="active" data-tsfilter="all">All event</li>
                            <li data-tsfilter="fitness">Fitness tips</li>
                            <li data-tsfilter="motivation">Motivation</li>
                            <li data-tsfilter="workout">Workout</li>
                        </ul>
                    </div>
                </div>
                -->
            </div>
            <?php require_once BASE_PATH . '/templates/class-timetable-view.php'; ?>
        </div>
    </section>
    <!-- Class Timetable Section End -->

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>
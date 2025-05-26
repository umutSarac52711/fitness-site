<?php
$page_title = "Class Details";
require_once '../../config.php';          
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';
 include BASE_PATH . '/templates/sidebar-latest.php'; 

?>

    <!-- Class Details Section Begin -->
    <section class="class-details-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="class-details-text">
                        <div class="cd-pic">
                            <img src="<?= BASE_URL ?>/assets/img/classes/class-details/class-detailsl.jpg" alt="">
                        </div>
                        <div class="cd-text">
                            <div class="cd-single-item">
                                <h3>University Fitness Programs</h3>
                                <p>Our university gym offers a wide range of fitness programs tailored to student needs. From high-intensity interval training (HIIT) and strength conditioning to yoga and Zumba, there's something for everyone. We provide access to state-of-the-art equipment and dedicated spaces for various activities, ensuring you have the resources to achieve your health and wellness goals while balancing your academic life.</p>
                            </div>
                            <div class="cd-single-item">
                                <h3>Our Certified Trainers</h3>
                                <p>Meet our team of certified and experienced trainers, many of whom are kinesiology students or graduates. They are passionate about fitness and dedicated to helping you on your journey, whether you're a beginner or an experienced athlete. Our trainers can assist with personalized workout plans, proper technique, and nutritional advice to ensure you get the most out of your gym sessions safely and effectively.</p>
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="col-lg-4 col-md-8">
                    <div class="sidebar-option">
                        <div class="so-categories">
                            <h5 class="title">Categories</h5>
                            <ul>
                                <li><a href="#">Yoga <span>12</span></a></li>
                                <li><a href="#">Running <span>32</span></a></li>
                                <li><a href="#">Weight Loss <span>86</span></a></li>
                                <li><a href="#">Cardio <span>25</span></a></li>
                                <li><a href="#">Body building <span>36</span></a></li>
                                <li><a href="#">Nutrition <span>15</span></a></li>
                            </ul>
                        </div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Class Details Section End -->

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


<?php
include_once BASE_PATH . '/templates/footer.php';
require_once BASE_PATH . '/templates/script.php';?>

<?php
$page_title = "Services";
require_once '../../config.php';          
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';
?>

    <!-- Services Section Begin -->
    <section class="services-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Our Campus Offerings</span>
                        <h2>YOUR UNIVERSITY FITNESS & WELLNESS CENTER</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 order-lg-1 col-md-6 p-0">
                    <div class="ss-pic">
                        <img src="<?= BASE_URL ?>/assets/img/services/services-1.jpg" alt="Student Fitness Programs">
                    </div>
                </div>
                <div class="col-lg-3 order-lg-2 col-md-6 p-0">
                    <div class="ss-text">
                        <h4>Student Fitness Programs</h4>
                        <p>Customized training plans and guidance to help you achieve your fitness goals effectively.</p>
                        <a href="#">Explore</a>
                    </div>
                </div>
                <div class="col-lg-3 order-lg-3 col-md-6 p-0">
                    <div class="ss-pic">
                        <img src="<?= BASE_URL ?>/assets/img/services/services-2.jpg" alt="Group Exercise Classes">
                    </div>
                </div>
                <div class="col-lg-3 order-lg-4 col-md-6 p-0">
                    <div class="ss-text">
                        <h4>Group Exercise Classes</h4>
                        <p>Join dynamic classes like Yoga, Zumba, Cycling, and HIIT, led by certified instructors.</p>
                        <a href="#">Explore</a>
                    </div>
                </div>
                <div class="col-lg-3 order-lg-8 col-md-6 p-0">
                    <div class="ss-pic">
                        <img src="<?= BASE_URL ?>/assets/img/services/services-4.jpg" alt="Open Gym & Modern Equipment">
                    </div>
                </div>
                <div class="col-lg-3 order-lg-7 col-md-6 p-0">
                    <div class="ss-text second-row">
                        <h4>Open Gym & Modern Equipment</h4>
                        <p>Access our well-equipped gym featuring a wide range of cardio and strength machines.</p>
                        <a href="#">Explore</a>
                    </div>
                </div>
                <div class="col-lg-3 order-lg-6 col-md-6 p-0">
                    <div class="ss-pic">
                        <img src="<?= BASE_URL ?>/assets/img/services/services-3.jpg" alt="Intramural & Club Sports">
                    </div>
                </div>
                <div class="col-lg-3 order-lg-5 col-md-6 p-0">
                    <div class="ss-text second-row">
                        <h4>Intramural & Club Sports</h4>
                        <p>Participate in friendly competition with intramural leagues or join a university sports club.</p>
                        <a href="#">Explore</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Services Section End -->

    <!-- Banner Section Begin 
    <section class="banner-section set-bg" data-setbg="<?= BASE_URL ?>/assets/img/banner-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="bs-text service-banner">
                        <h2>Exercise until the body obeys.</h2>
                        <div class="bt-tips">Where health, beauty and fitness meet.</div>
                        <a href="https://www.youtube.com/watch?v=EzKkl64rRbM" class="play-btn video-popup"><i
                                class="fa fa-caret-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    Banner Section End -->

    
    
    <?php require_once BASE_PATH . '/templates/pricing.php'; ?>
    
    

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>
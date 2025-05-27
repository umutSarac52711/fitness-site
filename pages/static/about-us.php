<?php
$page_title = "About Us";
require_once '../../config.php';          
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';
/*include BASE_PATH . '/templates/trainers-section.php';*/
?>

<!-- ChooseUs Section Begin -->
    <section class="chooseus-section spad" id="ChooseUs">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Why chose us?</span>
                        <h2>PUSH YOUR LIMITS FORWARD</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-034-stationary-bike"></span>
                        <h4>State-of-the-Art Equipment</h4>
                        <p>Experience cutting-edge machines and weights designed to meet the rigorous standards of today's university athletes.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-033-juice"></span>
                        <h4>Tailored Student Nutrition</h4>
                        <p>Access customized nutrition guidance and meal plans that keep you energized during long study sessions and intense workouts.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-002-dumbell"></span>
                        <h4>Personalized Training Programs</h4>
                        <p>Benefit from expert coaching and training sessions developed specifically for university students looking to balance fitness with academics.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-014-heart-beat"></span>
                        <h4>Campus Community & Wellness</h4>
                        <p>Join a vibrant community fostering health, fitness, and academic excellence, with events and challenges that unite students on campus.</p>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>
    <!-- ChooseUs Section End -->

<!-- About Us Section Begin -->
<section class="aboutus-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="about-video set-bg" data-setbg="<?= BASE_URL ?>/assets/img/about-us.jpg">
                    <a href="https://www.youtube.com/watch?v=EzKkl64rRbM" class="play-btn video-popup"><i
                            class="fa fa-caret-right"></i></a>
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="about-text">
                    <div class="section-title">
                        <span>About Us</span>
                        <h2>Empowering Student Fitness and Wellness</h2>
                    </div>
                    <div class="at-desc">
                        <p>Our university gym is dedicated to providing students with top-notch facilities and programs to support their physical and mental well-being. We offer a comprehensive range of equipment, expert-led classes, and personalized training options designed to fit into your busy academic schedule. Join our community to stay active, healthy, and focused on your success.</p>
                    </div>
                    <div class="about-bar">
                        <div class="ab-item">
                            <p>Body building</p>
                            <div id="bar1" class="barfiller">
                                <span class="fill" data-percentage="80"></span>
                                <div class="tipWrap">
                                    <span class="tip"></span>
                                </div>
                            </div>
                        </div>
                        <div class="ab-item">
                            <p>Training</p>
                            <div id="bar2" class="barfiller">
                                <span class="fill" data-percentage="85"></span>
                                <div class="tipWrap">
                                    <span class="tip"></span>
                                </div>
                            </div>
                        </div>
                        <div class="ab-item">
                            <p>Fitness</p>
                            <div id="bar3" class="barfiller">
                                <span class="fill" data-percentage="75"></span>
                                <div class="tipWrap">
                                    <span class="tip"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Section End -->

<?php include BASE_PATH . '/templates/trainers-section.php'; ?>


<!-- Banner Section Begin -->
<section class="banner-section set-bg" data-setbg="<?= BASE_URL ?>/assets/img/banner-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="bs-text">
                    <h2>Elevate Your Student Wellness</h2>
                    <div class="bt-tips">Your on-campus hub for health and peak performance.</div>
                    <a href="<?= BASE_URL ?>/pages/memberships.php" class="primary-btn btn-normal">Explore Memberships</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<?php
// Fetch approved testimonials
// global $pdo; // Should be available from config.php included at the top
$testimonials_data = []; // Initialize
try {
    // Assuming \'approved\' is the status for publicly visible testimonials
    // Fetches the 3 most recent approved testimonials
    $testimonial_stmt = $pdo->prepare("SELECT name, quote, rating, photo FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC LIMIT 3");
    $testimonial_stmt->execute();
    $testimonials_data = $testimonial_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    set_flash_message("Error fetching testimonials for about-us page: " . $e->getMessage(), "danger");
}
?>
<!-- Testimonial Section Begin -->
<section class="testimonial-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span>Testimonial</span>
                    <h2>Our cilent say</h2>
                </div>
            </div>
        </div>
        <?php display_flash_message(); ?>
        <div class="ts_slider owl-carousel">
            <?php if (!empty($testimonials_data)): ?>
                <?php foreach ($testimonials_data as $testimonial_item): ?>
                    <?php
                    // Determine image URL for each testimonial
                    $current_image_url = BASE_URL . '/assets/img/testimonial/testimonial-1.jpg'; // Default image
                    if (!empty($testimonial_item['photo'])) {
                        $image_path = ltrim($testimonial_item['photo'], '/'); // Normalize path
                        if (file_exists(BASE_PATH . '/' . $image_path)) { // Check against absolute server path
                            $current_image_url = BASE_URL . '/' . htmlspecialchars($testimonial_item['photo']);
                        } else {
                            // Optionally log or set a flash message if a specific testimonial's image is missing
                            error_log("Testimonial image not found for " . htmlspecialchars($testimonial_item['name']) . ": " . BASE_PATH . $image_path);
                            // You might want to avoid setting a flash message for each missing image in a loop
                            // set_flash_message("Image for testimonial '" . htmlspecialchars($testimonial_item['name']) . "' not found.", "warning");
                        }
                    }
                    ?>
                    <div class="ts_item">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <div class="ti_pic">
                                    <img src="<?= $current_image_url ?>" alt="Testimonial from <?= htmlspecialchars($testimonial_item['name']) ?>">
                                </div>
                                <div class="ti_text">
                                    <p><?= nl2br(htmlspecialchars($testimonial_item['quote'])) ?></p>
                                    <h5><?= htmlspecialchars($testimonial_item['name']) ?></h5>
                                    <div class="tt-rating">
                                        <?php 
                                        $rating = (int)$testimonial_item['rating'];
                                        for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $rating): ?>
                                                <i class="fa fa-star"></i>
                                            <?php else: ?>
                                                <i class="fa fa-star-o"></i> <?php // Using fa-star-o for empty star rating representation ?>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="ts_item">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No testimonials to display at the moment. Check back soon!</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- Testimonial Section End -->

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>
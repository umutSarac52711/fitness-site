<?php
$page_title = "404! Page Not Found";
require_once '../../config.php';          
?>

<!-- 404 Section Begin -->
<section class="section-404">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-404">
                    <h1>404</h1>
                    <h3>Opps! This page Could Not Be Found!</h3>
                    <p>Sorry bit the page you are looking for does not exist, have been removed or name changed</p>
                    <form action="#" class="search-404">
                        <input type="text" placeholder="Enter your keyword">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                    <a href="<?= BASE_URL ?>/index.php"><i class="fa fa-home"></i> Go back home</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- 404 Section End -->


<?php 
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/index.php';
require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>

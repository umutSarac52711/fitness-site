<?php
/* templates/header.php */
if (!isset($page_title)) { $page_title = 'Fitness Site'; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="Gym Template">
    <meta name="keywords" content="Gym, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gym | Template</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css" type="text/css">

  <!-- Additional Styling -->
  <link href="<?= BASE_URL ?>/assets/css/custom.css" rel="stylesheet">
</head>
<body>

<!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Offcanvas Menu Section Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="canvas-close">
            <i class="fa fa-close"></i>
        </div>
        <div class="canvas-search search-switch">
            <i class="fa fa-search"></i>
        </div>
        <nav class="canvas-menu mobile-menu">
            <ul>
                <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>/pages/about-us.php">About Us</a></li>
                <li><a href="<?= BASE_URL ?>/pages/classes.php">Classes</a></li>
                <li><a href="<?= BASE_URL ?>/pages/services.php">Services</a></li>
                <li><a href="<?= BASE_URL ?>/pages/team.php">Our Team</a></li>
                <li><a href="#">Pages</a>
                    <ul class="dropdown">
                        <li><a href="<?= BASE_URL ?>/pages/about-us.php">About us</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/class-timetable.php">Classes timetable</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/bmi-calculator.php">Bmi calculate</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/team.php">Our team</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/gallery.php">Gallery</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/blog.php">Our blog</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/404.php">404</a></li>
                    </ul>
                </li>
                <li><a href="<?= BASE_URL ?>/pages/contact.php">Contact</a></li>
            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
        <div class="canvas-social">
            <a href="#"><i class="fa fa-facebook"></i></a>
            <a href="#"><i class="fa fa-twitter"></i></a>
            <a href="#"><i class="fa fa-youtube-play"></i></a>
            <a href="#"><i class="fa fa-instagram"></i></a>
        </div>
    </div>
    <!-- Offcanvas Menu Section End -->

    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="<?= BASE_URL ?>/index.php">
                            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="nav-menu">
                        <ul>
                            <li class="active"><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/about-us.php">About Us</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/class-details.php">Classes</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/services.php">Services</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/team.php">Our Team</a></li>
                            <li><a href="#">Pages</a>
                                <ul class="dropdown">
                                    <li><a href="<?= BASE_URL ?>/pages/about-us.php">About us</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/class-timetable.php">Classes timetable</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/bmi-calculator.php">Bmi calculate</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/team.php">Our team</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/gallery.php">Gallery</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/blog.php">Our blog</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/404.php">404</a></li>
                                </ul>
                            </li>
                            <li><a href="<?= BASE_URL ?>/pages/contact.php">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="top-option">
                        <div class="to-search search-switch">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="to-social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-youtube-play"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="canvas-open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header End -->
    
    <!-- page-specific content starts here -->

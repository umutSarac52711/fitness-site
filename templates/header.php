
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
                <li><a href="<?= BASE_URL ?>/pages/static/about-us.php">About Us</a></li>
                <li><a href="<?= BASE_URL ?>/pages/static/class-details.php">Classes</a></li>
                <li><a href="<?= BASE_URL ?>/pages/static/services.php">Services</a></li>
                <li><a href="<?= BASE_URL ?>/pages/static/team.php">Our Team</a></li>
                <li><a href="#">Pages</a>
                    <ul class="dropdown">
                        <li><a href="<?= BASE_URL ?>/pages/static/about-us.php">About us</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/class-timetable.php">Classes timetable</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/bmi-calculator.php">Bmi calculate</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/team.php">Our team</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/gallery.php">Gallery</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/blog.php">Our blog</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/404.php">404</a></li>
                    </ul>
                </li>
                <li><a href="<?= BASE_URL ?>/pages/static/contact.php">Contact</a></li>
                
                <?php if (empty($_SESSION['user'])): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">Register</a></li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                    <?= htmlspecialchars($_SESSION['user']['name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
                    </ul>
                  </li>
                <?php endif; ?>

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
                            <li><a href="<?= BASE_URL ?>/pages/static/about-us.php">About Us</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/static/class-details.php">Classes</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/static/services.php">Services</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/static/team.php">Our Team</a></li>
                            <li><a href="#">Pages</a>
                                <ul class="dropdown">
                                    <li><a href="<?= BASE_URL ?>/pages/static/about-us.php">About us</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/class-timetable.php">Classes timetable</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/bmi-calculator.php">Bmi calculate</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/team.php">Our team</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/gallery.php">Gallery</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/blog.php">Our blog</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/404.php">404</a></li>
                                </ul>
                            </li>
                            <li><a href="<?= BASE_URL ?>/pages/static/contact.php">Contact</a></li>

                            <?php if (empty($_SESSION['user'])): ?>
                                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">Register</a></li>
                            <?php else: ?>
                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                <?= htmlspecialchars($_SESSION['user']['name']) ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
                                </ul>
                              </li>
                            <?php endif; ?>
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

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role']==='admin'): ?>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10 d-flex align-items-center justify-content-center">
                    <nav class="nav-menu w-100" style="display: flex; justify-content: center;">
                        <ul style="display: flex; gap: 1.5rem; align-items: center; margin: 0 auto;">
                            <li><a href="<?= BASE_URL ?>/pages/users/list.php">Users</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/plans/list.php">Plans</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/classes/list.php">Classes</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/posts/list.php">Posts</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/progress/list.php">Progress Logs</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/testimonials/list.php">Testimonials</a></li>
                            <li class="nav-item dropdown">
                              <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?>
                              </a>
                              <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
                              </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="canvas-open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
        <?php endif?>

    </header>
    <!-- Header End -->
    
    <!-- page-specific content starts here -->

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
                <li<?= isCurrentPage('/index.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                <li<?= isCurrentPage('/pages/static/about-us.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/about-us.php">About Us</a></li>
                <li<?= isCurrentPage('/pages/static/class-details.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/class-details.php">Classes</a></li>
                <li<?= isCurrentPage('/pages/static/services.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/services.php">Services</a></li>
                <li<?= isCurrentPage('/pages/static/blog.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/blog.php">Blog</a></li>
                <li><a href="#">Pages</a>
                    <ul class="dropdown">
                        <li><a href="<?= BASE_URL ?>/pages/static/class-timetable.php">Classes Timetable</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/bmi-calculator.php">BMI Calculator</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/gallery.php">Gallery</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/static/404.php">404</a></li>
                    </ul>
                </li>
                <li><a href="<?= BASE_URL ?>/pages/static/contact.php">Contact</a></li>
                
                <?php if (!empty($_SESSION['user'])): // User is logged in ?>
                    <?php 
                        $displayName = !empty(trim($_SESSION['user']['full_name'] ?? '')) 
                                       ? $_SESSION['user']['full_name'] 
                                       : (!empty(trim($_SESSION['user']['username'] ?? ''))
                                          ? $_SESSION['user']['username']
                                          : ($_SESSION['user']['role'] === 'admin' ? 'Admin' : 'User'));
                        
                        $accountLink = ($_SESSION['user']['role'] === 'admin') 
                                       ? '#' // Admin name in main nav can link to # or a general admin dashboard landing
                                       : BASE_URL.'/pages/auth/account.php';
                    ?>
                    <li>
                        <a href="<?= $accountLink ?>"><?= htmlspecialchars($displayName) ?></a>
                        <ul class="dropdown">
                            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] !== 'admin'): ?>
                                <li><a href="<?= BASE_URL ?>/pages/auth/account.php">My Account</a></li>
                            <?php endif; ?>
                            <li><a href="<?= BASE_URL ?>/pages/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: // User is a guest ?>
                    <li<?= isCurrentPage('/login.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/auth/login.php">Login</a></li>
                    <li<?= isCurrentPage('/register.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/auth/register.php">Register</a></li>
                <?php endif; ?>

            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
        <div class="canvas-social">
            <a href="https://www.facebook.com/TEDUniversity"><i class="fa fa-facebook"></i></a>
            <a href="https://twitter.com/ted_uni"><i class="fa fa-twitter"></i></a>
            <a href="https://www.youtube.com/user/TEDUChannel"><i class="fa fa-youtube-play"></i></a>
            <a href="https://www.instagram.com/universityted/"><i class="fa fa-instagram"></i></a>
        </div>
    </div>
    <!-- Offcanvas Menu Section End -->

    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="<?= BASE_URL ?>/index.php">
                            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Fitness Logo" height="45" padding-top="auto">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="nav-menu">
                        <ul>
                            <li<?= isCurrentPage('/index.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                            <li<?= isCurrentPage('/pages/static/about-us.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/about-us.php">About Us</a></li>
                            <li<?= isCurrentPage('/pages/static/class-details.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/class-details.php">Classes</a></li>
                            <li<?= isCurrentPage('/pages/static/services.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/services.php">Services</a></li>
                            <li<?= isCurrentPage('/pages/static/blog.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/static/blog.php">Blog</a></li>
                            <li><a href="#">Pages</a>
                                <ul class="dropdown">
                                    <li><a href="<?= BASE_URL ?>/pages/static/class-timetable.php">Classes Timetable</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/bmi-calculator.php">BMI Calculator</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/gallery.php">Gallery</a></li>
                                    <li><a href="<?= BASE_URL ?>/pages/static/404.php">404</a></li>
                                </ul>
                            </li>
                            <?php if (!empty($_SESSION['user'])): // User is logged in ?>
                                <?php 
                                    $displayName = !empty(trim($_SESSION['user']['full_name'] ?? '')) 
                                                   ? $_SESSION['user']['full_name'] 
                                                   : (!empty(trim($_SESSION['user']['username'] ?? ''))
                                                      ? $_SESSION['user']['username']
                                                      : ($_SESSION['user']['role'] === 'admin' ? 'Admin' : 'User'));
                                    
                                    $accountLink = ($_SESSION['user']['role'] === 'admin') 
                                                   ? '#' // Admin name in main nav can link to # or a general admin dashboard landing
                                                   : BASE_URL.'/pages/auth/account.php';
                                ?>
                                <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] !== 'admin'): ?>
                                
                                    <li>
                                    <a href="<?= $accountLink ?>"><?= htmlspecialchars($displayName) ?></a>
                                    <ul class="dropdown">
                                            <li><a href="<?= BASE_URL ?>/pages/auth/account.php">My Account</a></li>
                                        <li><a href="<?= BASE_URL ?>/pages/auth/logout.php">Logout</a></li>
                                    </ul>
                                </li>

                                <?php endif; ?>
                            <?php else: // User is a guest ?>
                                <li<?= isCurrentPage('login.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/auth/login.php">Login</a></li>
                                <li<?= isCurrentPage('register.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/auth/register.php">Register</a></li>
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
                            <a href="https://www.facebook.com/TEDUniversity"><i class="fa fa-facebook"></i></a>
                            <a href="https://twitter.com/ted_uni"><i class="fa fa-twitter"></i></a>
                            <a href="https://www.youtube.com/user/TEDUChannel"><i class="fa fa-youtube-play"></i></a>
                            <a href="https://www.instagram.com/universityted/"><i class="fa fa-instagram"></i></a>
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
                            <li>
                                <?php 
                                    // Determine display name for admin in admin bar
                                    $adminDisplayName = !empty(trim($_SESSION['user']['full_name'] ?? '')) 
                                                      ? $_SESSION['user']['full_name'] 
                                                      : (!empty(trim($_SESSION['user']['username'] ?? ''))
                                                         ? $_SESSION['user']['username']
                                                         : 'Admin');
                                ?>
                                <a href="<?= BASE_URL ?>/pages/auth/account.php "><?= htmlspecialchars($adminDisplayName) ?></a>
                              <ul class="dropdown">
                                <li><a href="<?= BASE_URL ?>/pages/auth/logout.php">Logout</a></li>
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

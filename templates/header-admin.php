<?php
// templates/header-admin.php

// Helper function to check if current page matches a link
function isActivePage($link) {
    $currentPage = $_SERVER['PHP_SELF'];
    $link = str_replace(BASE_URL, '', $link);
    return (strpos($currentPage, $link) !== false);
}
?>

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
                <li<?= isActivePage('/pages/users/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/users/list.php">Users</a></li>
                <li<?= isActivePage('/pages/plans/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/plans/list.php">Plans</a></li>
                <li<?= isActivePage('/pages/classes/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/classes/list.php">Classes</a></li>
                <li<?= isActivePage('/pages/posts/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/posts/list.php">Posts</a></li>
                <li<?= isActivePage('/pages/progress/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/progress/list.php">Progress Logs</a></li>
                <li<?= isActivePage('/pages/testimonials/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/testimonials/list.php">Testimonials</a></li>
                <li>
                  <a href="#">
                    <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?>
                  </a>
                  <ul class="dropdown">
                    <li><a href="<?= BASE_URL ?>/logout.php">Logout</a></li>
                  </ul>
                </li>
            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
    </div>
    <!-- Offcanvas Menu Section End -->

    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="row justify-content-center">                <div class="col-lg-10 d-flex align-items-center justify-content-center">
                    <nav class="nav-menu w-100" style="display: flex; justify-content: center;">
                        <ul style="display: flex; gap: 1.5rem; align-items: center; margin: 0 auto;">
                            <li<?= isActivePage('/pages/users/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/users/list.php">Users</a></li>
                            <li<?= isActivePage('/pages/plans/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/plans/list.php">Plans</a></li>
                            <li<?= isActivePage('/pages/classes/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/classes/list.php">Classes</a></li>
                            <li<?= isActivePage('/pages/posts/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/posts/list.php">Posts</a></li>
                            <li<?= isActivePage('/pages/progress/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/progress/list.php">Progress Logs</a></li>
                            <li<?= isActivePage('/pages/testimonials/list.php') ? ' class="active"' : '' ?>><a href="<?= BASE_URL ?>/pages/testimonials/list.php">Testimonials</a></li>
                            <li><a href="#"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></a>
                              <ul class="dropdown">
                                <li><a href="<?= BASE_URL ?>/logout.php">Logout</a></li>
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
    </header>
    <!-- Header End -->
    <!-- page-specific content starts here -->

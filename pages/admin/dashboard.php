<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';

require_admin(); // Ensure only admins can access

$page_title = 'Admin Dashboard';

require_once BASE_PATH . '/templates/file-start.php';
?>

<div class="admin-dashboard-header" style="background-color: #2a2f34;position: fixed; left: 0; top: 0; width: 100%; padding: 25px 15px; box-sizing: border-box; z-index: 1000; color: #fff; display: flex; justify-content: space-between; align-items: center;">
    <h1 class="h3 mb-0" style="color: #fff;"><?php echo htmlspecialchars($page_title); ?></h1>
    <div class="admin-user-dropdown dropdown">
        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: #fff; text-decoration: none;">
            <i class="fa fa-user" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'); ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" style="background-color: #343a40; border-color: #454d55;">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php" style="color: #adb5bd;">Normal World</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/auth/account.php" style="color: #adb5bd;">Profile</a></li>
            <li><hr class="dropdown-divider" style="border-top: 1px solid #454d55;"></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/pages/auth/logout.php" style="color: #adb5bd;">Logout</a></li>
        </ul>
    </div>
</div>

<div class="admin-content-area-wrapper">
    <div class="main-content container admin-main-content-block" style="padding-top: 20px;"> <?php // Added padding-top ?>
        <?php display_flash_message(); ?>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card card-bg-custom-1 h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">Manage registered users and trainers.</p>
                        <a href="<?= BASE_URL ?>/pages/users/list.php" class="btn mt-auto">Go to Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card card-bg-custom-2 h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Classes</h5>
                        <p class="card-text">Manage fitness classes and schedules.</p>
                        <a href="<?= BASE_URL ?>/pages/classes/list.php" class="btn mt-auto">Go to Classes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card card-bg-custom-3 h-100 light-card-bg"> <?php // light-card-bg for potentially lighter custom bg ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Membership Plans</h5>
                        <p class="card-text">Manage subscription plans.</p>
                        <a href="<?= BASE_URL ?>/pages/plans/list.php" class="btn mt-auto">Go to Plans</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card card-bg-custom-4 h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Blog Posts</h5>
                        <p class="card-text">Manage blog content.</p>
                        <a href="<?= BASE_URL ?>/pages/posts/list.php" class="btn mt-auto">Go to Posts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card card-bg-custom-5 h-100 light-card-bg"> <?php // light-card-bg for potentially lighter custom bg ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Progress Logs</h5>
                        <p class="card-text">View and manage user progress.</p>
                        <a href="<?= BASE_URL ?>/pages/progress/list.php" class="btn mt-auto">Go to Progress</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card dashboard-card card-bg-custom-6 h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Testimonials</h5>
                        <p class="card-text">Manage customer testimonials.</p>
                        <a href="<?= BASE_URL ?>/pages/testimonials/list.php" class="btn mt-auto">Go to Testimonials</a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

        <h2 class="h4 mb-3">Analytics (Placeholder)</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-secondary" role="alert" style="background-color: #444; border-color: #555; color: #ccc;">
                    Analytics and reporting features will be implemented here. This section can display charts, key metrics (e.g., new user registrations, popular classes), or quick reports.
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once BASE_PATH . '/templates/script.php'; ?>

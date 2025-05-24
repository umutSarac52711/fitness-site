<?php
// filepath: c:\\xampp\\htdocs\\fitness\\pages\\auth\\account.php

// Enhanced error reporting for development - REMOVE FOR PRODUCTION
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$page_title = "Account";
require_once '../../config.php'; // Defines $pdo, BASE_PATH, BASE_URL
require_once BASE_PATH . '/includes/auth.php'; // For session_start() and require_login()

require_login(); // Redirects if not logged in

$user_id = $_SESSION['user']['id'];
$user_data = null;
$error_message = '';
$success_message = '';

// Helper function to translate upload errors
function file_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder for uploads.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk. Check permissions.';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload.';
        default:
            return 'Unknown upload error.';
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // CSRF token validation would be good here
    $full_name = trim($_POST['full_name']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $dob = !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null;

    // Fetch current profile picture filename
    $stmt_current_pic = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt_current_pic->execute([$user_id]);
    $current_pic_data = $stmt_current_pic->fetch();
    $profile_picture_filename = $current_pic_data ? $current_pic_data['profile_picture'] : null;

    if (isset($_FILES['profilePicUpload']) && $_FILES['profilePicUpload']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = BASE_PATH . '/uploads/profile_pictures/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $error_message .= " Failed to create profile picture directory. Check server permissions. Path: {$upload_dir}";
            }
        }
        $tmp_name = $_FILES['profilePicUpload']['tmp_name'];
        $original_filename = basename($_FILES['profilePicUpload']['name']);
        $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_extension, $allowed_extensions)) {
            if ($_FILES['profilePicUpload']['size'] <= 2097152) // Max 2MB
            {
                $new_filename = "user_{$user_id}_" . uniqid() . ".{$file_extension}";
                $destination = "{$upload_dir}{$new_filename}";
                if (move_uploaded_file($tmp_name, $destination)) {
                    // Delete old profile picture if it exists and is different
                    if ($profile_picture_filename && $profile_picture_filename !== $new_filename && file_exists("{$upload_dir}{$profile_picture_filename}")) {
                        unlink("{$upload_dir}{$profile_picture_filename}");
                    }
                    $profile_picture_filename = $new_filename;
                } else {
                    $error_message .= " Error moving uploaded profile picture. Check permissions for {$upload_dir}.";
                }
            } else {
                $error_message .= " Profile picture is too large (max 2MB).";
            }
        } else {
            $error_message .= " Invalid file type for profile picture (allowed: jpg, jpeg, png, gif).";
        }


    } elseif (isset($_FILES['profilePicUpload']) && $_FILES['profilePicUpload']['error'] != UPLOAD_ERR_NO_FILE) {
        $error_code = $_FILES['profilePicUpload']['error'];
        $error_message .= " Error uploading profile picture: " . file_upload_error_message($error_code) . " (Code: {$error_code})";
    }

    if (empty($full_name)) {
        $error_message = "Full name cannot be empty.{$error_message}";
    }

    // Proceed with DB update if full_name is not empty (even if there were file errors, non-critical ones)
    if (!empty($full_name)) {
        try {
            $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone_number = ?, address = ?, date_of_birth = ?, profile_picture = ?, updated_at = NOW() WHERE id = ?");
            $update_stmt->execute([$full_name, $phone_number, $address, $dob, $profile_picture_filename, $user_id]);
            if (empty($error_message)) {
                $success_message = "Profile updated successfully!";
            } else {
                $success_message = "Profile details updated, but please check error messages regarding profile picture.";
            }
        } catch (PDOException $e) {
            $error_message .= " Error updating profile in DB: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ")";
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['currentPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New password and confirmation do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "New password must be at least 6 characters long.";
    } else {
        $stmt_pass = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pass->execute([$user_id]);
        $current_user_db_data = $stmt_pass->fetch(PDO::FETCH_ASSOC);

        if ($current_user_db_data && password_verify($current_password, $current_user_db_data['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            try {
                $update_pass_stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                $update_pass_stmt->execute([$new_password_hash, $user_id]);
                $success_message = "Password changed successfully!";
            } catch (PDOException $e) {
                $error_message = "Error changing password: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ")";
            }
        } else {
            $error_message = "Incorrect current password.";
        }
    }
}

// Fetch user data (always fetch fresh data after potential updates)
try {
    $stmt = $pdo->prepare("SELECT u.*, p.name as plan_name
                           FROM users u 
                           LEFT JOIN plans p ON u.membership_plan_id = p.id 
                           WHERE u.id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        // This should not happen if require_login() is effective
        error_log("Critical: User data not found for logged-in user ID: {$user_id}");
        $error_message = "User data could not be loaded. Please contact support. (User ID: {$user_id})";
        $user_data = []; // Prevent further errors in template
    }
} catch (PDOException $e) {
    error_log("Database error fetching user data for account page: " . $e->getMessage());
    $error_message = "Could not retrieve user data due to a database issue: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ")";
    $user_data = []; // Ensure $user_data is an array
}

// Prepare display variables
$display_username = isset($user_data['username']) ? htmlspecialchars($user_data['username']) : 'N/A';
$display_name = isset($user_data['full_name']) ? htmlspecialchars($user_data['full_name']) : 'N/A';
$display_email = isset($user_data['email']) ? htmlspecialchars($user_data['email']) : 'N/A';
$display_phone = isset($user_data['phone_number']) ? htmlspecialchars($user_data['phone_number']) : '';
$display_joined_date = isset($user_data['created_at']) ? date("F j, Y", strtotime($user_data['created_at'])) : 'N/A';
$display_address = isset($user_data['address']) ? htmlspecialchars($user_data['address']) : '';
$display_dob_form = isset($user_data['date_of_birth']) ? date("Y-m-d", strtotime($user_data['date_of_birth'])) : '';
$display_dob_profile = isset($user_data['date_of_birth']) ? date("F j, Y", strtotime($user_data['date_of_birth'])) : 'N/A';

$profile_pic_path = $user_data['profile_picture'] ?? null;
if ($profile_pic_path && file_exists(BASE_PATH . '/uploads/profile_pictures/' . $profile_pic_path)) {
    $display_profile_pic = BASE_URL . '/uploads/profile_pictures/' . htmlspecialchars($profile_pic_path);
} else {
    $display_profile_pic = BASE_URL . '/img/team/default-avatar.png'; // Ensure this default image exists
}

// Membership details
$display_membership_plan = isset($user_data['plan_name']) ? htmlspecialchars($user_data['plan_name']) : 'No Plan Assigned';
$display_membership_status = 'N/A';
$display_membership_status_color = '#757575'; // Default grey
$display_membership_valid_until = 'N/A';
$display_membership_next_billing = 'N/A';

if (isset($user_data['membership_start_date']) && isset($user_data['membership_end_date'])) {
    $today = new DateTime();
    $endDate = new DateTime($user_data['membership_end_date']);
    if ($endDate >= $today) {
        $display_membership_status = 'Active';
        $display_membership_status_color = '#4CAF50'; // Green
    } else {
        $display_membership_status = 'Expired';
        $display_membership_status_color = '#f44336'; // Red
    }
    $display_membership_valid_until = date("F j, Y", strtotime($user_data['membership_end_date']));
    if ($display_membership_status === 'Active') {
        $display_membership_next_billing = date("F j, Y", strtotime($user_data['membership_end_date'] . " +1 day")); // Simplified
    }
} elseif (isset($user_data['membership_plan_id'])) {
    $display_membership_status = 'Pending Activation / Info Missing';
    $display_membership_status_color = '#ff9800'; // Orange
} else {
    $display_membership_status = 'No Active Membership';
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
?>
    <!-- Account Section Begin -->
    <section class="account-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <div class="account-sidebar">
                        <div class="profile-pic-container">
                            <img src="<?= $display_profile_pic ?>?t=<?= time() ?>" alt="User Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;"> <!-- Added style for consistency & cache buster -->
                            <h4 class="user-name"><?= $display_name ?></h4>
                            <p class="user-email-display"><?= $display_email ?></p>
                        </div>
                        <nav class="account-nav">
                            <ul>
                                <li><a href="#profile-details" class="active"><i class="fa fa-user"></i> Profile Details</a></li>
                                <li><a href="#edit-profile"><i class="fa fa-pencil"></i> Edit Profile</a></li>
                                <li><a href="#membership-details"><i class="fa fa-id-card-o"></i> Membership</a></li>
                                <li><a href="#change-password"><i class="fa fa-key"></i> Change Password</a></li>
                                <li><a href="<?= BASE_URL ?>/pages/auth/logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="account-content">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;"><?= htmlspecialchars($success_message) ?></div>
                        <?php endif; ?>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;"><strong>Error:</strong><br><?=nl2br(htmlspecialchars($error_message)) ?></div>
                        <?php endif; ?>

                        <!-- Profile Details -->
                        <div id="profile-details" class="account-content-section">
                            <h3 class="content-title">My Profile</h3>
                            <div class="info-group">
                                <strong>Full Name:</strong>
                                <p><?= $display_name ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Username:</strong>
                                <p><?= $display_username ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Email Address:</strong>
                                <p><?= $display_email ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Phone Number:</strong>
                                <p><?= $display_phone ?: 'N/A' ?></p>
                            </div>
                             <div class="info-group">
                                <strong>Date of Birth:</strong>
                                <p><?= $display_dob_profile ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Address:</strong>
                                <p><?= $display_address ?: 'N/A' ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Joined Date:</strong>
                                <p><?= $display_joined_date ?></p>
                            </div>
                        </div>

                        <!-- Edit Profile Form -->
                         <div id="edit-profile" class="account-content-section" style="margin-top: 40px;">
                            <h3 class="content-title">Edit Profile</h3>
                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#edit-profile" method="POST" class="account-form" enctype="multipart/form-data">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fullName">Full Name</label>
                                            <input type="text" id="fullName" name="full_name" value="<?= $display_name === 'N/A' ? '' : $display_name ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" id="email" name="email" value="<?= $display_email === 'N/A' ? '' : $display_email ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="text" id="phone" name="phone_number" value="<?= $display_phone ?>">
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dob">Date of Birth</label>
                                            <input type="date" id="dob" name="date_of_birth" value="<?= $display_dob_form ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" name="address" value="<?= $display_address ?>">
                                </div>
                                <div class="form-group">
                                    <label for="profilePicUpload">Change Profile Picture (Max 2MB: JPG, PNG, GIF)</label>
                                    <input type="file" id="profilePicUpload" name="profilePicUpload" style="background: transparent; border: none; color: #c4c4c4; height: auto; padding-left:0;">
                                    <?php if ($user_data['profile_picture']): ?>
                                    <small>Current: <?= htmlspecialchars($user_data['profile_picture']) ?>. Upload new to replace.</small>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="primary-btn account-btn">Save Changes</button>
                            </form>
                        </div>

                        <!-- Membership Details -->
                        <div id="membership-details" class="account-content-section" style="margin-top: 40px;">
                            <h3 class="content-title">Membership Details</h3>
                            <div class="info-group">
                                <strong>Current Plan:</strong>
                                <p><?= $display_membership_plan ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Status:</strong>
                                <p style="color: <?= $display_membership_status_color ?>; font-weight: bold;"><?= $display_membership_status ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Valid Until:</strong>
                                <p><?= $display_membership_valid_until ?></p>
                            </div>
                            <div class="info-group">
                                <strong>Next Billing Date:</strong>
                                <p><?= $display_membership_next_billing ?></p>
                            </div>
                            <!-- <a href="#" class="primary-btn account-btn" style="margin-top: 15px;">Manage Subscription</a> -->
                        </div>
                        
                        <!-- Change Password Form -->
                        <div id="change-password" class="account-content-section" style="margin-top: 40px;">
                            <h3 class="content-title">Change Password</h3>
                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#change-password" method="POST" class="account-form">
                                <input type="hidden" name="change_password" value="1">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" id="currentPassword" name="currentPassword" required>
                                </div>
                                <div class="form-group">
                                    <label for="newPassword">New Password (min 6 characters)</label>
                                    <input type="password" id="newPassword" name="newPassword" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                                </div>
                                <button type="submit" class="primary-btn account-btn">Update Password</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Account Section End -->

<?php require_once BASE_PATH . '/templates/footer.php';?>

    <!-- Js Plugins -->
    <script src="<?= BASE_URL ?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?= BASE_URL ?>/js/bootstrap.min.js"></script>
    <script src="<?= BASE_URL ?>/js/jquery.magnific-popup.min.js"></script>
    <script src="<?= BASE_URL ?>/js/masonry.pkgd.min.js"></script>
    <script src="<?= BASE_URL ?>/js/jquery.barfiller.js"></script>
    <script src="<?= BASE_URL ?>/js/jquery.slicknav.js"></script>
    <script src="<?= BASE_URL ?>/js/owl.carousel.min.js"></script>
    <script src="<?= BASE_URL ?>/js/main.js"></script>

    <script>
        $(document).ready(function(){
            function showTargetSection(target) {
                if (!target || !$(target).length) { // Check if target is valid
                    target = '#profile-details'; // Default to profile-details
                }
                
                // Active class for nav
                $('.account-nav ul li a').removeClass('active');
                $('.account-nav ul li a[href="' + target + '"]').addClass('active');
                
                // Show/hide content sections
                $('.account-content-section').hide(); 
                $(target).fadeIn(); 
            }

            $('.account-nav ul li a').on('click', function(e){
                var href = $(this).attr('href');
                // Only process for internal fragment links
                if (href.startsWith('#')) { 
                    e.preventDefault();
                    showTargetSection(href);
                    // Update hash without jumping, for styling and bookmarking
                    if(history.pushState) {
                        history.pushState(null, null, href);
                    } else {
                        window.location.hash = href;
                    }
                }
                // For external links (like logout), let the default action proceed.
            });

            // On page load, check hash and show corresponding section or default
            var hash = window.location.hash;
            if (hash && $(hash).length && $(hash).hasClass('account-content-section')) {
                showTargetSection(hash);
            } else {
                // Default to profile-details if no hash, invalid hash, or hash not for a section
                showTargetSection('#profile-details');
            }

            // Clear messages after a delay or on next tab click to prevent them from sticking
            if ($('.alert-success').length || $('.alert-danger').length) {
                setTimeout(function() {
                    $('.alert-success, .alert-danger').fadeOut();
                }, 5000); // Disappear after 5 seconds
            }
        });
    </script>

</body>
</html>
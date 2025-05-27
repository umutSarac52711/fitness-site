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

error_log("Account page accessed by user ID: " . ($_SESSION['user']['id'] ?? 'not set'));

$user_id = $_SESSION['user']['id'];
$user_data = null;
$error_message = '';
$success_message = '';

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
        }    }
}

// Handle Add Progress Log
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_progress_log'])) {
    error_log("Progress log submission attempt by user ID: $user_id");
    
    $log_date = trim($_POST['log_date']);
    $log_weight = !empty($_POST['log_weight']) ? (float)$_POST['log_weight'] : null;
    $log_height = !empty($_POST['log_height']) ? (float)$_POST['log_height'] : null;
    $log_body_fat = !empty($_POST['log_body_fat']) ? (float)$_POST['log_body_fat'] : null;
    $log_notes = trim($_POST['log_notes']);

    if (empty($log_date)) {
        $error_message = "Date is required for progress log.";
        error_log("Progress log failed: Date missing for user ID: $user_id");
    } else {
        try {
            $sql_add_log = 'INSERT INTO progress_logs (user_id, date, weight, height, body_fat, notes) VALUES (?, ?, ?, ?, ?, ?)';
            $stmt_add_log = $pdo->prepare($sql_add_log);
            $stmt_add_log->execute([$user_id, $log_date, $log_weight, $log_height, $log_body_fat, $log_notes]);
            $success_message = "Progress log added successfully!";
            error_log("Progress log added successfully for user ID: $user_id, date: $log_date");
        } catch (PDOException $e) {
            error_log("Error adding progress log for user $user_id: " . $e->getMessage());
            $error_message = "Failed to add progress log: " . $e->getMessage();
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
    $error_message = "Could not retrieve user data due to a database issue: " . $e->getMessage() . " (SQLSTATE: " . $e->getCode() . ")";    $user_data = []; // Ensure $user_data is an array
}

// Fetch latest progress log for display
$latest_progress_log = null;
try {
    error_log("Fetching latest progress log for user ID: $user_id");
    $stmt_latest_progress = $pdo->prepare("SELECT weight, height, body_fat, date 
                                           FROM progress_logs
                                           WHERE user_id = ?
                                           ORDER BY date DESC, id DESC
                                           LIMIT 1");
    $stmt_latest_progress->execute([$user_id]);
    $latest_progress_log = $stmt_latest_progress->fetch(PDO::FETCH_ASSOC);
    error_log("Latest progress log fetched for user ID: $user_id - " . ($latest_progress_log ? 'Found' : 'Not found'));
} catch (PDOException $e) {
    error_log("Database error fetching latest progress log for user $user_id: " . $e->getMessage());
}

// Fetch all progress logs for the user
$all_progress_logs = [];
try {
    error_log("Fetching all progress logs for user ID: $user_id");
    $stmt_all_progress = $pdo->prepare("SELECT id, date, weight, height, body_fat, notes
                                        FROM progress_logs
                                        WHERE user_id = ?
                                        ORDER BY date DESC, id DESC");
    $stmt_all_progress->execute([$user_id]);
    $all_progress_logs = $stmt_all_progress->fetchAll(PDO::FETCH_ASSOC);
    error_log("Progress logs fetched for user ID: $user_id - Count: " . count($all_progress_logs));
} catch (PDOException $e) {
    error_log("Database error fetching all progress logs for user $user_id: " . $e->getMessage());
    $error_message .= " Could not retrieve your progress logs.";
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
} else {    $display_profile_pic = BASE_URL . '/img/team/default-avatar.png'; // Ensure this default image exists
}

// Prepare display variables for latest progress
$display_latest_weight = $latest_progress_log && isset($latest_progress_log['weight']) ? htmlspecialchars($latest_progress_log['weight']) . ' kg' : 'N/A';
$display_latest_height = $latest_progress_log && isset($latest_progress_log['height']) ? htmlspecialchars($latest_progress_log['height']) . ' cm' : 'N/A';
$display_latest_bmi = 'N/A'; // BMI is calculated, not fetched directly
if ($latest_progress_log && !empty($latest_progress_log['weight']) && !empty($latest_progress_log['height']) && $latest_progress_log['height'] > 0) {
    $height_m_latest = $latest_progress_log['height'] / 100;
    $display_latest_bmi = htmlspecialchars(round($latest_progress_log['weight'] / ($height_m_latest * $height_m_latest), 2));
}
$display_latest_body_fat = $latest_progress_log && isset($latest_progress_log['body_fat']) ? htmlspecialchars($latest_progress_log['body_fat']) . ' %' : 'N/A';
$display_latest_progress_date_info = $latest_progress_log && isset($latest_progress_log['date']) ? '(As of ' . date("M j, Y", strtotime($latest_progress_log['date'])) . ')' : '';

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
                        <nav class="account-nav">                            <ul>
                                <li><a href="#profile-details" class="active"><i class="fa fa-user"></i> Profile Details</a></li>
                                <li><a href="#edit-profile"><i class="fa fa-pencil"></i> Edit Profile</a></li>
                                <li><a href="#membership-details"><i class="fa fa-id-card-o"></i> Membership</a></li>
                                <li><a href="#my-progress"><i class="fa fa-line-chart"></i> My Progress</a></li>
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
                            </div>                            <div class="info-group">
                                <strong>Joined Date:</strong>
                                <p><?= $display_joined_date ?></p>
                            </div>
                            
                            <!-- Current Vitals Section -->
                            <h3 class="content-title" style="margin-top: 30px;">Current Vitals <?= $display_latest_progress_date_info ?></h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <strong>Weight:</strong>
                                        <p><?= $display_latest_weight ?></p>
                                    </div>
                                    <div class="info-group">
                                        <strong>Height:</strong>
                                        <p><?= $display_latest_height ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <strong>BMI:</strong>
                                        <p><?= $display_latest_bmi ?></p>
                                    </div>
                                    <div class="info-group">
                                        <strong>Body Fat %:</strong>
                                        <p><?= $display_latest_body_fat ?></p>
                                    </div>
                                </div>
                            </div>
                            <p style="font-size: 12px; color: #666; margin-top: 10px;"><em>This information is based on your latest progress log. Update it in the "My Progress" section.</em></p>
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
                            <!-- <a href="#" class="primary-btn account-btn" style="margin-top: 15px;">Manage Subscription</a> -->                        </div>
                        
                        <!-- My Progress -->
                        <div id="my-progress" class="account-content-section" style="margin-top: 40px;">
                            <h3 class="content-title">My Progress</h3>
                            
                            <!-- Add New Progress Log Form -->
                            <div style="margin-bottom: 30px;">
                                <h4 style="margin-bottom: 15px;">Add New Log</h4>
                                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#my-progress" method="POST" class="account-form">
                                    <input type="hidden" name="add_progress_log" value="1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="log_date">Date *</label>
                                                <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="log_weight">Weight (kg)</label>
                                                <input type="number" step="0.01" id="log_weight" name="log_weight" placeholder="e.g., 70.5" oninput="calculateBmiAccount()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="log_height">Height (cm)</label>
                                                <input type="number" step="0.01" id="log_height" name="log_height" placeholder="e.g., 175" oninput="calculateBmiAccount()">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="log_body_fat">Body Fat (%)</label>
                                                <input type="number" step="0.01" id="log_body_fat" name="log_body_fat" placeholder="e.g., 15.5">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="log_notes">Notes (optional)</label>
                                        <textarea id="log_notes" name="log_notes" rows="3" placeholder="Any additional notes about your progress..."></textarea>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 15px;">
                                        <label>BMI (calculated automatically):</label>
                                        <input type="text" id="calculated_bmi_display" readonly style="background: #f8f9fa; color: #666;" placeholder="Enter weight and height above">
                                    </div>
                                    <button type="submit" class="primary-btn account-btn">Add Progress Log</button>
                                </form>
                            </div>
                            
                            <!-- Progress Logs List -->
                            <div>
                                <h4 style="margin-bottom: 15px;">My Progress History</h4>
                                <?php if (!empty($all_progress_logs)): ?>
                                    <div style="overflow-x: auto;">
                                        <div style="min-width: 600px;">
                                            <?php foreach ($all_progress_logs as $log): ?>
                                                <?php
                                                // Calculate BMI for display for each log entry
                                                $bmi_display = '-';
                                                if (!empty($log['weight']) && !empty($log['height']) && $log['height'] > 0) {
                                                    $height_m = $log['height'] / 100;
                                                    $bmi_display = round($log['weight'] / ($height_m * $height_m), 2);
                                                }
                                                ?>
                                                <div style="border: 1px solid #ddd; margin-bottom: 15px; padding: 15px; border-radius: 5px; background: #f8f9fa;">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <strong>Date:</strong><br>
                                                                    <?= htmlspecialchars(date("M j, Y", strtotime($log['date']))) ?>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <strong>Weight:</strong><br>
                                                                    <?= htmlspecialchars($log['weight'] ?? '-') ?> kg
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <strong>Height:</strong><br>
                                                                    <?= htmlspecialchars($log['height'] ?? '-') ?> cm
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <strong>BMI:</strong><br>
                                                                    <?= $bmi_display ?>
                                                                </div>
                                                            </div>
                                                            <div class="row" style="margin-top: 10px;">
                                                                <div class="col-sm-3">
                                                                    <strong>Body Fat:</strong><br>
                                                                    <?= htmlspecialchars($log['body_fat'] ?? '-') ?>%
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <strong>Notes:</strong><br>
                                                                    <?= nl2br(htmlspecialchars($log['notes'] ?? '-')) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" style="text-align: right;">
                                                            <a href="<?= BASE_URL ?>/pages/progress/edit_user_log.php?id=<?= $log['id'] ?>" class="primary-btn account-btn" style="margin-right: 5px; margin-bottom: 5px; padding: 5px 10px; font-size: 12px;">Edit</a>
                                                            <a href="<?= BASE_URL ?>/pages/progress/delete_user_log.php?id=<?= $log['id'] ?>" class="primary-btn account-btn" style="background: #dc3545; margin-bottom: 5px; padding: 5px 10px; font-size: 12px;" onclick="return confirm('Are you sure you want to delete this log? This action cannot be undone.');">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p style="text-align: center; color: #666; padding: 20px; background: #f8f9fa; border-radius: 5px;">
                                        You haven't logged any progress yet. Add your first progress log above to get started!
                                    </p>
                                <?php endif; ?>
                            </div>
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
            }            // Clear messages after a delay or on next tab click to prevent them from sticking
            if ($('.alert-success').length || $('.alert-danger').length) {
                setTimeout(function() {
                    $('.alert-success, .alert-danger').fadeOut();
                }, 5000); // Disappear after 5 seconds
            }
        });
        
        // BMI calculation function for progress log form
        function calculateBmiAccount() {
            const weight = parseFloat(document.getElementById('log_weight').value);
            const height = parseFloat(document.getElementById('log_height').value);
            const bmiDisplay = document.getElementById('calculated_bmi_display');
            
            if (weight > 0 && height > 0) {
                const heightInMeters = height / 100;
                const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(2);
                bmiDisplay.value = bmi;
                console.log('BMI calculated:', bmi);
            } else {
                bmiDisplay.value = '';
            }
        }
    </script>

</body>
</html>
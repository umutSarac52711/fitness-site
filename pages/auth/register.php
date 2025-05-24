<?php
require_once '../../config.php';
require_once BASE_PATH.'/includes/functions.php';

$errors = [];
$old_input = [];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    $old_input = $_POST;

    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? ''); // Changed from name
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors['username'] = 'Username already taken.';
        }
    }

    if (empty($full_name)) {
        $errors['full_name'] = 'Full name is required.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email already registered.';
        }
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) { // Example: min length
        $errors['password'] = 'Password must be at least 6 characters long.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Updated SQL to use full_name and password, and added username
        $sql  = 'INSERT INTO users (username, full_name, email, password, role, is_active) 
                 VALUES (?, ?, ?, ?, "member", 1)'; // Assuming new users are active by default
        try {
            $pdo->prepare($sql)->execute([$username, $full_name, $email, $hash]);

            // auto-login
            $id  = $pdo->lastInsertId();
            // Fetch by id, ensure all necessary fields for login() are selected
            $stmt = $pdo->prepare("SELECT id, username, full_name, email, role, is_active FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC); // Force FETCH_ASSOC
            if ($u) {
                require_once BASE_PATH.'/includes/auth.php';
                login($u); // Ensure login() function is compatible with the fetched user array
                header('Location: '.BASE_URL.'/index.php'); exit;
            } else {
                $errors['form'] = "Could not retrieve user after registration.";
            }
        } catch (PDOException $e) {
            // Handle potential SQL errors, e.g., unique constraint violation if not caught above
            if ($e->getCode() == '23000') { // Integrity constraint violation
                 if (strpos($e->getMessage(), 'users_username_unique') !== false) {
                    $errors['username'] = 'This username is already taken.';
                } elseif (strpos($e->getMessage(), 'users_email_unique') !== false) {
                    $errors['email'] = 'This email address is already registered.';
                } else {
                    $errors['form'] = "An error occurred during registration. Please try again.";
                }
            } else {
                $errors['form'] = "Database error: " . $e->getMessage();
            }
        }
    }
}

$page_title='Register';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
?>

<!-- Register Section Begin -->
<section class="contact-section spad" style="padding-top:180px; padding-bottom:60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="section-title contact-title text-center mb-4">
          <span>Join Us</span>
          <h2>Create Your Account</h2>
        </div>
        <?php if (!empty($errors['form'])): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($errors['form']) ?></div>
        <?php endif; ?>
        <div class="leave-comment">
          <form method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="mb-3">
              <input name="username" type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" placeholder="Username" value="<?= htmlspecialchars($old_input['username'] ?? '') ?>" required>
              <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['username']) ?></div>
              <?php else: ?>
                <div class="invalid-feedback">Please enter your username.</div>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <input name="full_name" type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" placeholder="Full Name" value="<?= htmlspecialchars($old_input['full_name'] ?? '') ?>" required> <!-- Changed name to full_name -->
              <?php if (isset($errors['full_name'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['full_name']) ?></div>
              <?php else: ?>
                <div class="invalid-feedback">Please enter your full name.</div>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <input name="email" type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="Email" value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" required>
              <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['email']) ?></div>
              <?php else: ?>
                <div class="invalid-feedback">Please enter a valid email.</div>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <input name="password" type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Password" required>
              <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['password']) ?></div>
              <?php else: ?>
                <div class="invalid-feedback">Please enter a password.</div>
              <?php endif; ?>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-success btn-block">Create account</button> <!-- Ensure type="submit" -->
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Register Section End -->

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>

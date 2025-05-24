<?php
require_once '../../config.php';
require_once BASE_PATH.'/includes/auth.php';
require_once BASE_PATH.'/includes/functions.php';

$error=''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    $login_identifier = trim($_POST['login_identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_identifier) || empty($password)) {
        $error = 'Username/Email and Password are required.';
    } else {
        // Determine if login_identifier is email or username
        $field = filter_var($login_identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Updated SQL to use password (hashed) and select necessary fields for login()
        $sql="SELECT id, username, full_name, email, password, role, is_active FROM users WHERE $field = ?";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$login_identifier]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC); // Force FETCH_ASSOC
        if ($u && password_verify($password, $u['password'])) { // Changed $u['password_hash'] to $u['password']
            if (!$u['is_active']) {
                $error = 'Your account is inactive. Please contact support.';
            } else {
                login($u); // Ensure login() function is compatible with the fetched user array
                // Redirect to a dashboard or previous page if intended
                // For now, redirecting to index.php as before
                $redirect_url = $_SESSION['intended_url'] ?? BASE_URL.'/index.php';
                unset($_SESSION['intended_url']);
                header('Location: '. $redirect_url); 
                exit;
            }
        } else {
            $error='Invalid login credentials.';
        }
    }
}

$page_title='Login';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
?>

<!-- Login Section Begin -->
<section class="contact-section spad" style="padding-top:180px; padding-bottom:60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="section-title contact-title text-center mb-4">
          <span>Welcome Back</span>
          <h2>Login to Your Account</h2>
        </div>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger text-center"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <div class="leave-comment">
          <form method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="mb-3">
              <input name="login_identifier" type="text" class="form-control" placeholder="Username or Email" value="<?= htmlspecialchars($_POST['login_identifier'] ?? '') ?>" required>
              <div class="invalid-feedback">Please enter your username or email.</div>
            </div>
            <div class="mb-3">
              <input name="password" type="password" class="form-control" placeholder="Password" required>
              <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-block">Login</button> <!-- Ensure type="submit" -->
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Login Section End -->

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>

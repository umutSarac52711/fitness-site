<?php
require_once 'config.php';
require_once BASE_PATH.'/includes/auth.php';
require_once BASE_PATH.'/includes/functions.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    $sql='SELECT * FROM users WHERE email=?';
    $u=$pdo->prepare($sql); $u->execute([$_POST['email']]);
    $u=$u->fetch();
    if ($u && password_verify($_POST['password'],$u['password_hash'])) {
        login($u);
        header('Location: '.BASE_URL.'/index.php'); exit;
    }
    $error='Invalid login';
}
$page_title='Login';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
?>

<!-- Login Section Begin -->
<section class="contact-section spad" style="padding-top:120px; padding-bottom:60px;">
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
              <input name="email" type="email" class="form-control" placeholder="Email" required>
              <div class="invalid-feedback">Please enter your email.</div>
            </div>
            <div class="mb-3">
              <input name="password" type="password" class="form-control" placeholder="Password" required>
              <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary btn-block">Login</button>
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

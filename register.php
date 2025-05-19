<?php
require_once 'config.php';
require_once BASE_PATH.'/includes/functions.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql  = 'INSERT INTO users (name,email,password_hash,role)
             VALUES (?,?,?, "member")';
    $pdo->prepare($sql)->execute([$_POST['name'],$_POST['email'],$hash]);

    // auto-login
    $id  = $pdo->lastInsertId();
    $u   = $pdo->query("SELECT * FROM users WHERE id=$id")->fetch();
    require_once BASE_PATH.'/includes/auth.php';
    login($u);
    header('Location: '.BASE_URL.'/index.php'); exit;
}

$page_title='Register';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
?>

<!-- Register Section Begin -->
<section class="contact-section spad" style="padding-top:60px; padding-bottom:60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="section-title contact-title text-center mb-4">
          <span>Join Us</span>
          <h2>Create Your Account</h2>
        </div>
        <div class="leave-comment">
          <form method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="mb-3">
              <input name="name" type="text" class="form-control" placeholder="Full Name" required>
              <div class="invalid-feedback">Please enter your name.</div>
            </div>
            <div class="mb-3">
              <input name="email" type="email" class="form-control" placeholder="Email" required>
              <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
              <input name="password" type="password" class="form-control" placeholder="Password" required>
              <div class="invalid-feedback">Please enter a password.</div>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-success btn-block">Create account</button>
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

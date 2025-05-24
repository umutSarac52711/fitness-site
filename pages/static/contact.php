<?php
$page_title = "Contact";
require_once '../../config.php';
require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';
?>

    <!-- Contact Section Begin -->
    <section class="contact-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title contact-title">
                        <span>Contact Us</span>
                        <h2>GET IN TOUCH</h2>
                    </div>
                    <div class="contact-widget">
                        <div class="cw-text">
                            <i class="fa fa-map-marker"></i>
                            <p>TED University. Ziya Gökalp Caddesi No:48<br/> 06420, Kolej Çankaya - Ankara</p>
                        </div>
                        <div class="cw-text">
                            <i class="fa fa-mobile"></i>
                            <ul>
                                <li>+90 (312) 585 03 62</li>
                            </ul>
                        </div>
                        <div class="cw-text email">
                            <i class="fa fa-envelope"></i>
                            <p>sti@tedu.edu.tr</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="leave-comment">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            // Retrieve submitted form data
                            $name = trim($_POST['name'] ?? '');
                            $email = trim($_POST['email'] ?? '');
                            $message = trim($_POST['message'] ?? '');

                            // Basic validation
                            if ($name && $email && $message) {
                                try {
                                    // Assume $pdo is available from config.php
                                    $stmt = $pdo->prepare("INSERT INTO contact (name, email, message) VALUES (:name, :email, :message)");
                                    $stmt->execute([
                                        ':name'    => $name,
                                        ':email'   => $email,
                                        ':message' => $message
                                    ]);
                                    echo "<p style=\"color:green;\">Thank you for contacting us.</p>";
                                } catch (PDOException $e) {
                                    // Check if error is caused by duplicate entry (unique constraint violation)
                                    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                                        echo "<p style=\"color:red;\">You've already submitted the same message.</p>";
                                    } else {
                                        echo "<p>Error: " . $e->getMessage() . "</p>";
                                    }
                                }
                            } else {
                                echo "<p>Please fill in all fields.</p>";
                            }
                        }
                        ?>
                        <form action="" method="post">
                            <input type="text" name="name" placeholder="Name">
                            <input type="email" name="email" placeholder="Email">
                            <textarea name="message" placeholder="Message"></textarea>
                            <button type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3059.829773913464!2d32.85928397565201!3d39.922825485552586!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d34e53cc243af7%3A0xafa8419945f5d098!2sTED%20University!5e0!3m2!1sen!2sbd!4v1748039629718!5m2!1sen!2sbd" 
                    width="600" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

<?php require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';?>
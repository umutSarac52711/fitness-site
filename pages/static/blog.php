<?php
$page_title = "Blog";
require_once '../../config.php';
require_once BASE_PATH . '/includes/auth.php'; // For is_logged_in() and session access
require_once BASE_PATH . '/includes/functions.php'; // For utility functions

$stmt = $pdo->query('SELECT * FROM posts ORDER BY id DESC');
$posts = $stmt->fetchAll();

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';
?>

    <!-- Blog Section Begin -->
    <section class="blog-section spad">
        <div class="container">
            <?php
            // Check if user is logged in and has admin or trainer role
            if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'trainer')) :
            ?>
                <div class="mb-4 text-right">
                    <a href="<?= BASE_URL ?>/pages/posts/create.php" class="primary-btn">Create New Post</a>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-8 p-0">
                    <?php
                    // Determine current page number from query parameter
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $posts_per_page = 5;
                    $total_posts = count($posts);
                    $offset = ($page - 1) * $posts_per_page;
                    $max = min($offset + $posts_per_page, $total_posts);

                    // Loop through only 5 posts for the current page
                    for ($i = $offset; $i < $max; $i++):
                        $post = $posts[$i];
                    ?>
                        <div class="blog-item">
                            <div class="bi-pic">
                                <img src="<?= BASE_URL ?>/assets/img/blog/blog-1.jpg" alt="">
                            </div>
                            <div class="bi-text">
                                <h5>
                                    <a href="<?= BASE_URL ?>/pages/static/blog-details.php?slug=<?= htmlspecialchars($post['slug']) ?>">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                </h5>
                                <ul>
                                    <li>by Admin</li>
                                    <li><?= htmlspecialchars($post['created_at']) ?></li>
                                    <li>20 Comment</li>
                                </ul>
                                <p><?= htmlspecialchars($post['content']) ?></p>
                            </div>
                        </div>
                    <?php endfor; ?>
                     
                    <?php
                    $total_pages = ceil($total_posts / $posts_per_page);
                    echo '<div class="blog-pagination">';

                    // Previous link
                    if ($page > 1) {
                        echo '<a href="?page=' . ($page - 1) . '">Previous</a>';
                    }

                    // Numbered page links
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $class = ($i === $page) ? ' class="active"' : '';
                        echo '<a' . $class . ' href="?page=' . $i . '">' . $i . '</a>';
                    }

                    // Next link
                    if ($page < $total_pages) {
                        echo '<a href="?page=' . ($page + 1) . '">Next</a>';
                    }

                    echo '</div>';
                    ?>

                </div>
            </div>
        </div>
    </section>
    <!-- Blog Section End -->

<?php require_once BASE_PATH . '/templates/footer.php';
require_once BASE_PATH . '/templates/script.php';?>
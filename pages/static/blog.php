<?php
$page_title = "Blog";
require_once '../../config.php';
require_once BASE_PATH . '/includes/auth.php'; // For is_logged_in() and session access
require_once BASE_PATH . '/includes/functions.php'; // For utility functions

// Attempt to load Parsedown for Markdown rendering
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Instantiate Parsedown if the class exists
$Parsedown = null;
if (class_exists('Parsedown')) {
    $Parsedown = new Parsedown();
}

// Updated SQL query to fetch author details and comment counts
$sql = "SELECT p.*, 
               u.username AS author_username, 
               u.full_name AS author_full_name, 
               (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        ORDER BY p.created_at DESC"; // Order by created_at for typical blog listing

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    display_flash_message(); // Display any flash messages


                    // Determine current page number from query parameter
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $posts_per_page = 5;
                    $total_posts = count($posts);
                    $offset = ($page - 1) * $posts_per_page;
                    $max = min($offset + $posts_per_page, $total_posts);

                    // Loop through only 5 posts for the current page
                    for ($i = $offset; $i < $max; $i++):
                        $post = $posts[$i];
                        $cover_image_url = BASE_URL . (!empty($post['cover_img']) ? htmlspecialchars($post['cover_img']) : '/assets/img/blog/default.webp');
                        $author_display_name = !empty($post['author_full_name']) ? htmlspecialchars($post['author_full_name']) : htmlspecialchars($post['author_username'] ?? 'Admin');
                        $comment_count_display = (int)($post['comment_count'] ?? 0);
                    ?>
                        <div class="blog-item">
                            <div class="bi-pic">
                                <img src="<?= $cover_image_url ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                            </div>
                            <div class="bi-text">
                                <h5>
                                    <a href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= htmlspecialchars($post['slug']) ?>">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                </h5>
                                <ul>
                                    <li>by <?= $author_display_name ?></li>
                                    <li><?= date('M d, Y', strtotime($post['created_at'])) ?></li>
                                    <li><?= $comment_count_display ?> Comment<?= ($comment_count_display != 1 ? 's' : '') ?></li>
                                </ul>
                                <?php 
                                // Admin Delete Button
                                if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') :
                                ?>
                                <div style="margin-top: 10px; margin-bottom: 10px;">
                                    <a href="<?= BASE_URL ?>/pages/posts/delete.php?id=<?= htmlspecialchars($post['id']) ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this post permanently? This action cannot be undone.');">
                                        Delete Post
                                    </a>
                                </div>
                                <?php 
                                endif;

                                if ($Parsedown) {
                                    echo $Parsedown->text($post['content']); // Render full content for now
                                } else {
                                    // Fallback if Parsedown is not available
                                    echo nl2br(htmlspecialchars($post['content'])); 
                                }
                                ?>
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
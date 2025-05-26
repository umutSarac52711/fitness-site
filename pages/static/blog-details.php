<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php'; // For potential author details or logged-in user features

// Attempt to load Parsedown for Markdown rendering
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

$post = null;
$author_name = 'Admin'; // Default author name
$page_title = "Blog Details"; // Default page title

if (!isset($_GET['slug'])) {
    set_flash_message('No blog post specified.', 'danger');
    redirect(BASE_URL . '/pages/static/blog.php');
}

$slug = $_GET['slug'];

try {
    $stmt = $pdo->prepare(
        'SELECT p.*, u.username AS author_username, u.full_name AS author_full_name, u.bio AS author_bio, u.profile_picture AS author_profile_picture ' .
        'FROM posts p ' .
        'LEFT JOIN users u ON p.author_id = u.id ' .
        'WHERE p.slug = :slug'
    );
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        set_flash_message('Blog post not found.', 'danger');
        redirect(BASE_URL . '/pages/static/blog.php');
    }

    $page_title = htmlspecialchars($post['title']);
    if (!empty($post['author_full_name'])) {
        $author_name = htmlspecialchars($post['author_full_name']);
    } elseif (!empty($post['author_username'])) {
        $author_name = htmlspecialchars($post['author_username']); // Default to username
    }
    // Note: The following duplicate 'if' block for $author_name was present in the previous read_file output.
    // It's harmless but redundant. I'm leaving it as is to match the surrounding context for the diff.
    if (!empty($post['author_full_name'])) {
        $author_name = htmlspecialchars($post['author_full_name']);
    }

    // Author bio and profile picture
    $author_bio = !empty($post['author_bio']) ? htmlspecialchars($post['author_bio']) : 'This author has not yet provided a bio.';
    $author_profile_pic_url = BASE_URL . '/assets/img/blog/details/default-profile.jpg'; // Default author profile pic
    if (!empty($post['author_profile_picture'])) {
        $author_pic_path = str_replace('\\', '/', $post['author_profile_picture']); // PHP str_replace('\\', '/', ...) to replace single backslashes
        if (strpos($author_pic_path, 'http') === 0) { // Check if it's a full URL
            $author_profile_pic_url = $author_pic_path;
        } else {
            // Construct path relative to the web root
            $web_relative_path = ltrim($author_pic_path, '/');
            if (strpos($web_relative_path, 'uploads/profile_pictures/') === 0) {
                // Path is already like 'uploads/profile_pictures/image.jpg'
                $full_system_path = BASE_PATH . '/' . $web_relative_path;
                $url_path = BASE_URL . '/' . $web_relative_path;
            } else {
                // Path might be just 'image.jpg', so prepend the uploads directory
                $full_system_path = BASE_PATH . '/uploads/profile_pictures/' . basename($web_relative_path);
                $url_path = BASE_URL . '/uploads/profile_pictures/' . basename($web_relative_path);
            }

            if (file_exists($full_system_path)) {
                $author_profile_pic_url = $url_path;
            }
            // If file_exists check fails, the default $author_profile_pic_url remains.
        }
    }

    // Fetch tags for this post
    $tags = [];
    try {
        $stmt_tags = $pdo->prepare(
            'SELECT t.name, t.slug ' .
            'FROM tags t ' .
            'JOIN post_tags pt ON t.id = pt.tag_id ' .
            'WHERE pt.post_id = :post_id ' .
            'ORDER BY t.name ASC'
        );
        $stmt_tags->execute([':post_id' => $post['id']]);
        $tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tags: " . $e->getMessage());
        // $tags will remain empty
    }

    // Fetch comments for this post
    $comments = [];
    $comment_count = 0;
    try {
        $stmt_comments = $pdo->prepare(
            'SELECT c.*, u.username AS comment_author_username, u.full_name AS comment_author_full_name, '.
            'COALESCE(u.profile_picture, \'assets/img/blog/details/default-profile.png\') AS comment_author_avatar ' . // Reverted to u.profile_picture
            'FROM comments c ' .
            'JOIN users u ON c.user_id = u.id ' .
            'WHERE c.post_id = :post_id ' .
            'ORDER BY c.created_at DESC'
        );
        $stmt_comments->execute([':post_id' => $post['id']]);
        $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
        $comment_count = count($comments);
    } catch (PDOException $e) {
        error_log("Error fetching comments: " . $e->getMessage());
        // $comments will remain empty, $comment_count will be 0
    }

} catch (PDOException $e) {
    error_log("Error fetching blog post: " . $e->getMessage());
    set_flash_message('Error loading blog post. Please try again later.', 'danger');
    redirect(BASE_URL . '/pages/static/blog.php');
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';


$cover_image_url = BASE_URL . '/assets/img/blog/details/details-hero.jpg'; // Default hero image
if (!empty($post['cover_img'])) {
    $cover_image_url = BASE_URL . htmlspecialchars($post['cover_img']);
}

$formatted_date = date("M d, Y", strtotime($post['created_at']));

// Prepare content for display (Markdown to HTML)
$display_content = 'Error loading content.';
if (class_exists('Parsedown')) {
    $Parsedown = new Parsedown();
    $display_content = $Parsedown->text($post['content']);
} else {
    // Fallback if Parsedown is not available: display raw content, escaping HTML
    // Or, display a message to install Parsedown
    $display_content = nl2br(htmlspecialchars($post['content']));
    // Optionally, add a note for admins:
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
        $display_content .= '<p><small class="text-muted"><em>Note: Parsedown library not found. Markdown will not be rendered. Please run `composer require erusev/parsedown`.</em></small></p>';
    }
}

?>

<!-- Blog Details Hero Section Begin -->
    <section class="blog-details-hero set-bg" data-setbg="<?= $cover_image_url ?>">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 p-0 m-auto">
                    <div class="bh-text">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <ul>
                            <li>by <?= $author_name ?></li>
                            <li><?= $formatted_date ?></li>
                            <li><?= $comment_count ?> Comment<?= ($comment_count !== 1) ? 's' : '' ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details Hero Section End -->

    <!-- Blog Details Section Begin -->
    <section class="blog-details-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 p-0 m-auto">
                    <div class="blog-details-text">
                        <div class="blog-details-title">
                             <!-- Content will be rendered by Parsedown or nl2br(htmlspecialchars()) -->
                        </div>
                        <?= $display_content ?>
                        
                        <?php /* Static content below this line can be kept or made dynamic later */ ?>
                        <?php if (class_exists('Parsedown') && !empty($post['cover_img']) && $post['cover_img'] !== '/assets/img/blog/blog-1.jpg') : ?>
                        <!-- Example of how you might show an inline image if Parsedown is not handling it and it's different from a generic one -->
                        <!-- This section might be redundant if your Markdown content includes images -->
                        <!-- <div class="blog-details-pic">
                            <div class="blog-details-pic-item">
                                <img src="<?= $cover_image_url ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                            </div>
                        </div> -->
                        <?php endif; ?>

                        <div class="blog-details-tag-share mt-4">
                            <div class="tags">
                                <?php if (!empty($tags)): ?>
                                    <?php foreach ($tags as $tag): ?>
                                        <a href="<?= BASE_URL ?>/pages/static/blog.php?tag=<?= htmlspecialchars($tag['slug']) ?>"><?= htmlspecialchars($tag['name']) ?></a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span>No tags available for this post.</span>
                                <?php endif; ?>
                            </div>
                            <div class="share">
                                <span>Share</span>
                                <a href="#"><i class="fa fa-facebook"></i> 82</a>
                                <a href="#"><i class="fa fa-twitter"></i> 24</a>
                                <a href="#"><i class="fa fa-envelope"></i> 08</a>
                            </div>
                        </div>
                        <div class="blog-details-author">
                            <div class="ba-pic">
                                <img src="<?= $author_profile_pic_url ?>" alt="<?= $author_name ?> Profile Picture" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;"> 
                            </div>
                            <div class="ba-text">
                                <h5><?= $author_name ?></h5>
                                <p><?= nl2br($author_bio) ?></p> 
                                <div class="bp-social">
                                    <a href="#"><i class="fa fa-facebook"></i></a>
                                    <a href="#"><i class="fa fa-twitter"></i></a>
                                    <a href="#"><i class="fa fa-google-plus"></i></a>
                                    <a href="#"><i class="fa fa-instagram"></i></a>
                                    <a href="#"><i class="fa fa-youtube-play"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comments Section Begin -->
                        <div class="row" id="comments-section">
                            <div class="col-lg-12"> <!-- Changed to col-lg-12 to span full width for comments list and form -->
                                <div class="comment-option">
                                    <h5 class="co-title"><?= $comment_count ?> Comment<?= ($comment_count !== 1) ? 's' : '' ?></h5>
                                    <?php if ($comment_count > 0): ?>
                                        <?php foreach ($comments as $comment): ?>
                                            <?php
                                                $commenter_name = !empty($comment['comment_author_full_name']) ? htmlspecialchars($comment['comment_author_full_name']) : htmlspecialchars($comment['comment_author_username']);
                                                
                                                // 1. Get path from DB & normalize to forward slashes
                                                $avatar_path_from_sql = htmlspecialchars($comment['comment_author_avatar']);
                                                $avatar_path_from_sql = str_replace('\\', '/', $avatar_path_from_sql);
                                                $avatar_base_path = '/uploads/profile_pictures';
                                                $avatar_path_from_sql = $avatar_base_path . '/' . ltrim($avatar_path_from_sql, '/');

                                                // 2. Define default image details (consistent with SQL COALESCE)
                                                $default_image_path_relative = 'assets/img/blog/details/default-profile.jpg';
                                                $default_image_url = BASE_URL . '/' . $default_image_path_relative;
                                                $default_image_filepath = BASE_PATH . '/' . $default_image_path_relative;
                                                
                                                $chosen_avatar_url = '';

                                                // 4. Is the path from SQL a custom one and not the default string itself?
                                                if (!empty($avatar_path_from_sql) && $avatar_path_from_sql !== $default_image_path_relative) {
                                                    // It's a custom path.
                                                    if (strpos($avatar_path_from_sql, 'http') === 0) {
                                                        // It's a full URL, assume it's valid.
                                                        $chosen_avatar_url = $avatar_path_from_sql;
                                                    } else {
                                                        // It's a relative local path. Check if the file exists.
                                                        // Ensure ltrim correctly handles the already normalized path.
                                                        if (file_exists($avatar_path_from_sql)) {
                                                            $chosen_avatar_url = BASE_URL . '/' . ltrim($avatar_path_from_sql, '/');
                                                        } else {
                                                            $custom_filepath = BASE_PATH . '/' . ltrim($avatar_path_from_sql, '/');
                                                        if (file_exists($custom_filepath)) {
                                                            $chosen_avatar_url = BASE_URL . '/' . ltrim($avatar_path_from_sql, '/');
                                                        }
                                                        }
                                                    }
                                                }

                                                // 5. If no valid custom avatar was chosen (or it was the default path string), try the primary default.
                                                if (empty($chosen_avatar_url)) {
                                                    $chosen_avatar_url = $default_image_url;
                                                }
                                                
                                                $comment_avatar_to_display = $chosen_avatar_url;
                                            ?>
                                            <div class="co-item">
                                                <div class="co-pic">
                                                    <img src="<?= $comment_avatar_to_display ?>" alt="<?= $commenter_name ?> avatar" style="width: 70px; height: 70px; border-radius: 50%;">
                                                </div>
                                                <div class="co-text">
                                                    <h6 style="color: cornflowerblue"><?= $commenter_name ?></h6>
                                                    <span style="color:aliceblue"><?= date("M d, Y, H:i", strtotime($comment['created_at'])) ?></span>
                                                    <p><?= nl2br(htmlspecialchars($comment['body'])) ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No comments yet. Be the first to comment!</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 mt-4" id="leave-comment-section"> <!-- Changed to col-lg-12 -->
                                <?php if (is_logged_in()): ?>
                                    <div class="leave-comment">
                                        <h5>Leave a comment</h5>
                                        <?php display_flash_message(); // Display flash messages if any ?>
                                        <form action="<?= BASE_URL ?>/pages/comments/add_comment.php" method="POST">
                                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                            <input type="hidden" name="post_slug_for_redirect" value="<?= htmlspecialchars($slug) ?>">
                                            <textarea name="comment_body" placeholder="Write your comment here..." required rows="5"></textarea>
                                            <button type="submit" class="site-btn">Submit Comment</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="leave-comment">
                                        <h5>Leave a comment</h5>
                                        <p>Please <a href="<?= BASE_URL ?>/pages/auth/login.php?redirect=<?= urlencode(BASE_URL . '/pages/static/blog-details.php?slug=' . $slug . '#leave-comment-section') ?>">login</a> to leave a comment.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Comments Section End -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details Section End -->

<?php 
require_once BASE_PATH . '/templates/footer.php'; 
require_once BASE_PATH . '/templates/script.php';
?>
<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php'; // For potential author details or logged-in user features


//PARSEDOWN
// Attempt to load Parsedown for Markdown rendering
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}


//Post initialization
$post = null;
$author_name = 'Admin'; // Default author name
$page_title = "Blog Details"; // Default page title

// Ensure the slug is provided in the URL
if (!isset($_GET['slug'])) {
    set_flash_message('No blog post specified.', 'danger');
    redirect(BASE_URL . '/pages/static/blog.php');
}

$slug = $_GET['slug'];


// Fetch the blog post from the database using the slug
try {

    // Prepare the SQL statement to fetch the post by slug
    // Note: Using PDO prepared statements to prevent SQL injection
    $stmt = $pdo->prepare(
        'SELECT p.*, u.username AS author_username, u.full_name AS author_full_name, u.bio AS author_bio, u.profile_picture AS author_profile_picture ' .
        'FROM posts p ' .
        'LEFT JOIN users u ON p.author_id = u.id ' .
        'WHERE p.slug = :slug'
    );

    // Execute the statement with the provided slug
    $stmt->execute([':slug' => $slug]);

    // Fetch the post data as an associative array
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the post was found
    if (!$post) {
        set_flash_message('Blog post not found.', 'danger');
        redirect(BASE_URL . '/pages/static/blog.php');
    }


    // Set the page title and author name 
    $page_title = htmlspecialchars($post['title']);
    if (!empty($post['author_full_name'])) {
        $author_name = htmlspecialchars($post['author_full_name']);
    } elseif (!empty($post['author_username'])) {
        $author_name = htmlspecialchars($post['author_username']); // Default to username
    }



    // Author bio
    $author_bio = !empty($post['author_bio']) ? htmlspecialchars($post['author_bio']) : 'This author has not yet provided a bio.';
    //An if statement to check if the author bio is empty, and if so, set a default message.
    


    // Author profile picture ----- contains a relative path to the profile picture by definition
        // If the author profile picture is not set, use a default image
    
        // Default author profile picture URL
    $author_profile_pic_url = BASE_URL . '/assets/img/default-profile.jpg'; // Default author profile pic
    
        // Check if the author profile picture is set and not empty
    if (!empty($post['author_profile_picture'])) {
        
        // Normalize the path to use forward slashes
        $author_pic_path = str_replace('\\', '/', $post['author_profile_picture']); // PHP str_replace('\\', '/', ...) to replace single backslashes
        
        // Construct path relative to the web root
        $web_relative_path = ltrim($author_pic_path, '/');
        
        //The path is just the file name, so we need to prepend the uploads directory
        // Construct the full system path and URL
            // BASE_PATH is the absolute path to the project root directory
            // BASE_URL is the base URL of the application
        $full_system_path = BASE_PATH . '/uploads/profile_pictures/' . basename($web_relative_path);
        $url_path = BASE_URL . '/uploads/profile_pictures/' . basename($web_relative_path);
        
        // Check if the file exists in the system
        if (file_exists($full_system_path)) {
            $author_profile_pic_url = $url_path;
        }
        else {
            // Log the error if the file does not exist
            error_log("Author profile picture not found: " . $full_system_path);
            // Optionally, you can set a flash message or handle this case differently
        }
    }


    // Tag processing
        // Initialize an empty array for processed tags
    $processed_tags = [];
        // Check if the post has tags and process them
    if (isset($post['tags']) && !empty($post['tags'])) {
        $raw_tags = explode(',', $post['tags']);
        
        //String processing to trim whitespace and remove empty tags
        foreach ($raw_tags as $tag_name) {
            $trimmed_tag = trim($tag_name);
            if (!empty($trimmed_tag)) {
                $processed_tags[] = $trimmed_tag;
            }
        }
    }
    // $processed_tags now contains an array of tag names


    // Comment fetching
        // Initialize comments array and count
    $comments = [];
    $comment_count = 0;
        // Fetch comments for the post
    try {
        // Standard SQL query to fetch comments for the post
        $stmt_comments = $pdo->prepare(
            'SELECT c.*, u.username AS comment_author_username, u.full_name AS comment_author_full_name, '.
            'COALESCE(u.profile_picture, \'assets/img/blog/details/default-profile.jpg\') AS comment_author_avatar ' . // Reverted to u.profile_picture
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
    /*redirect(BASE_URL . '/pages/static/blog.php');*/
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';

// Set the cover image URL, defaulting to a generic hero image if not set
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

                        <?php if (is_logged_in()): ?>
                            <?php
                            $current_user_id = $_SESSION['user']['id'] ?? null;
                            $current_user_role = $_SESSION['user']['role'] ?? null;
                            $is_admin = ($current_user_role === 'admin');
                            $is_author = ($current_user_id == $post['author_id']);
                            ?>
                            <?php if ($is_admin || $is_author): ?>
                                <div class="admin-actions mt-4 mb-4" style="border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 15px 0;">
                                    <a href="<?= BASE_URL ?>/pages/posts/edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary mr-2">
                                        <i class="fa fa-pencil"></i> Edit Post
                                    </a>
                                    <a href="<?= BASE_URL ?>/pages/posts/delete.php?id=<?= $post['id'] ?>&slug=<?= htmlspecialchars($post['slug']) ?>&source=details" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                                        <i class="fa fa-trash"></i> Delete Post
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
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
                                <?php if (!empty($processed_tags)): ?>
                                    <?php foreach ($processed_tags as $tag): ?>
                                        <a href="<?= BASE_URL ?>/pages/static/blog.php?tag=<?= htmlspecialchars(urlencode(strtolower(trim($tag)))) ?>"><?= htmlspecialchars($tag) ?></a>
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
                        <div class="row">
                            <div class="col-lg-12"> 
                                <!-- This div will be populated by AJAX -->
                                <div id="comments-section-ajax" data-post-slug="<?= htmlspecialchars($slug) ?>">
                                    <p class="small text-muted">Loading comments...</p> 
                                </div>
                            </div>
                        </div>
                        <!-- Comments Section End -->

                        <!-- Leave a Comment Section (remains, as form submission is separate) -->
                        <div class="row">
                            <div class="col-lg-12 mt-4" id="leave-comment-section"> 
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
                                        <p>Please <a href="<?= BASE_URL ?>/pages/auth/login.php?redirect=<?= urlencode(BASE_URL . '/pages/blog/blog-details.php?slug=' . $slug . '#leave-comment-section') ?>">login</a> to leave a comment.</p>
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
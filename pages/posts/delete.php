<?php
require_once __DIR__ . '/../../config.php'; // Defines BASE_PATH, $pdo, etc.
require_once BASE_PATH . '/includes/functions.php'; // Defines redirect, set_flash_message, etc.
require_once BASE_PATH . '/includes/auth.php'; // For is_logged_in() check

if (!is_logged_in()) {
    set_flash_message("You must be logged in to delete posts.", 'danger');
    redirect(BASE_URL . '/pages/auth/login.php');
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    set_flash_message("Invalid post ID.", 'danger');
    redirect(BASE_URL . '/pages/static/blog.php');
}

$post_id = (int)$_GET['id'];

// Fetch post details to check author and cover image
$stmt_check = $pdo->prepare("SELECT author_id, cover_img, slug FROM posts WHERE id = ?");
$stmt_check->execute([$post_id]);
$post = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    set_flash_message("Post not found.", 'danger');
    redirect(BASE_URL . '/pages/static/blog.php');
}

$current_user_id = $_SESSION['user']['id'] ?? null;
$current_user_role = $_SESSION['user']['role'] ?? null;
$is_admin = ($current_user_role === 'admin');
$is_author = ($current_user_id == $post['author_id']);

if (!$is_admin && !$is_author) {
    set_flash_message("You do not have permission to delete this post.", 'danger');
    $redirect_url = isset($post['slug']) ? (BASE_URL . '/pages/blog/blog-details.php?slug=' . htmlspecialchars($post['slug'])) : (BASE_URL . '/pages/static/blog.php');
    redirect($redirect_url);
}

// Delete cover image if it exists and is not a default one
if (!empty($post['cover_img'])) {
    // Construct the full path to the image file
    $cover_img_path = rtrim(BASE_PATH, '/') . '/uploads/blog_images/' . basename($post['cover_img']);
    
    // Prevent deleting a potential default image if it's stored with a generic name and shared
    $is_default_image = (basename($post['cover_img']) === 'default.webp'); // Example check

    if (!$is_default_image && file_exists($cover_img_path)) {
        if (!unlink($cover_img_path)) {
            error_log("Failed to delete cover image: " . $cover_img_path);
            set_flash_message("Post deleted, but failed to remove the cover image file.", 'warning');
        }
    }
}

// Delete the post from the database
$stmt_delete_post = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$post_deleted = $stmt_delete_post->execute([$post_id]);

if ($post_deleted) {
    // Also delete associated comments
    $stmt_delete_comments = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt_delete_comments->execute([$post_id]);

    set_flash_message("Post and associated comments deleted successfully.", 'success');
} else {
    set_flash_message("Failed to delete post. Please try again.", 'danger');
    error_log("Failed to delete post ID {$post_id}: " . print_r($stmt_delete_post->errorInfo(), true));
}

// Always redirect to the main blog page after deletion.
redirect(BASE_URL . '/pages/static/blog.php');
?>
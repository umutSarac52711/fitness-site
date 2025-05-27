<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

if (!is_logged_in()) {
    set_flash_message('You must be logged in to comment.', 'danger');
    // Try to redirect back to the post, or to login if slug isn't available for some reason
    $redirect_url = isset($_POST['post_slug_for_redirect']) ? 
                    BASE_URL . '/pages/blog/blog-details.php?slug=' . $_POST['post_slug_for_redirect'] . '#leave-comment-section' :
                    BASE_URL . '/pages/auth/login.php';
    redirect($redirect_url);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['post_id']) || empty($_POST['comment_body'])) {
        set_flash_message('Comment body cannot be empty.', 'danger');
        // Redirect back to the post detail page with an anchor to the comment form
        $redirect_url = isset($_POST['post_slug_for_redirect']) ? 
                        BASE_URL . '/pages/blog/blog-details.php?slug=' . $_POST['post_slug_for_redirect'] . '#leave-comment-section' :
                        BASE_URL . '/pages/static/blog.php'; // Fallback if slug is missing
        redirect($redirect_url);
    }

    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user']['id']; // Assumes user ID is stored in session
    $comment_body = trim($_POST['comment_body']);
    $post_slug_for_redirect = $_POST['post_slug_for_redirect'];

    if (empty($comment_body)) {
        set_flash_message('Comment body cannot be empty after trimming.', 'danger');
        redirect(BASE_URL . '/pages/blog/blog-details.php?slug=' . $post_slug_for_redirect . '#leave-comment-section');
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, body) VALUES (:post_id, :user_id, :body)");
        $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id,
            ':body' => $comment_body
        ]);

        set_flash_message('Comment added successfully!', 'success');
    } catch (PDOException $e) {
        error_log("Error adding comment: " . $e->getMessage());
        set_flash_message('Failed to add comment. Please try again.', 'danger');
    }

    // Redirect back to the post detail page with an anchor to the comments section
    redirect(BASE_URL . '/pages/blog/blog-details.php?slug=' . $post_slug_for_redirect . '#comments-section');

} else {
    // If not a POST request, redirect to blog page or homepage
    set_flash_message('Invalid request method.', 'warning');
    redirect(BASE_URL . '/pages/static/blog.php');
}
?>

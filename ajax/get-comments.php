<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/functions.php'; // For $pdo, date formatting, etc.

header('Content-Type: text/html; charset=utf-8');

$post_slug = $_GET['slug'] ?? null;

if (!$post_slug) {
    echo '<p class="text-danger">Error: Post slug not provided.</p>';
    exit;
}

try {
    // 1. Get post_id from slug
    $stmt_post = $pdo->prepare("SELECT id FROM posts WHERE slug = :slug");
    $stmt_post->execute([':slug' => $post_slug]);
    $post = $stmt_post->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo '<p class="text-muted">Post not found for comments.</p>';
        exit;
    }
    $post_id = $post['id'];

    // 2. Fetch comments for the post_id
    $stmt_comments = $pdo->prepare(
        'SELECT c.*, u.username AS comment_author_username, u.full_name AS comment_author_full_name, ' .
        'u.profile_picture AS comment_author_avatar ' .
        'FROM comments c ' .
        'JOIN users u ON c.user_id = u.id ' .
        'WHERE c.post_id = :post_id ' .
        'ORDER BY c.created_at DESC'
    );
    $stmt_comments->execute([':post_id' => $post_id]);
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
    $comment_count = count($comments);

?>
    <h5 class="co-title"><?= $comment_count ?> Comment<?= ($comment_count !== 1) ? 's' : '' ?></h5>
    <?php if ($comment_count > 0): ?>
        <?php foreach ($comments as $comment): ?>
            <?php
                $commenter_name = !empty($comment['comment_author_full_name']) ? htmlspecialchars($comment['comment_author_full_name']) : htmlspecialchars($comment['comment_author_username']);
                $default_avatar_url = BASE_URL . '/assets/img/blog/details/default-profile.jpg'; // Ensure this default image exists
                $user_avatar_path_db = $comment['comment_author_avatar'];

                $comment_avatar_to_display = $default_avatar_url;
                if (!empty($user_avatar_path_db)) {
                    $normalized_path = ltrim($user_avatar_path_db, '/');
                    if (file_exists(BASE_PATH . '/uploads/profile_pictures/' . $normalized_path)) {
                        $comment_avatar_to_display = BASE_URL . '/uploads/profile_pictures/' . htmlspecialchars($normalized_path);
                    }
                }
            ?>
            <div class="co-item">
                <div class="co-pic">
                    <img src="<?= $comment_avatar_to_display ?>" alt="<?= $commenter_name ?> avatar" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;">
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
<?php
} catch (PDOException $e) {
    error_log("AJAX get-comments PDOException: " . $e->getMessage());
    echo "<p class=\"text-danger\">Error loading comments. Database issue.</p>";
} catch (Throwable $e) {
    error_log("AJAX get-comments Throwable: " . $e->getMessage());
    echo "<p class=\"text-danger\">An unexpected error occurred while loading comments.</p>";
}
?>

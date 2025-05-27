<?php
require_once '../config.php';
header('Content-Type: text/html; charset=utf-8');

try {
    $result_array = get_latest_posts(5); // Fetches 5 posts for the structure
    $all_posts = $result_array['data'] ?? [];

    if (empty($all_posts)) {
        echo '<p class="small text-muted">No posts found.</p>';
        exit;
    }

    // Take the first post for the "latest-large" section
    $first_post = array_shift($all_posts);
    // The rest of the posts for the "latest-item" list
    $remaining_posts = $all_posts;
?>

<div class="so-latest">
    <h5 class="title">Latest posts</h5>
    <?php if ($first_post):
        // Ensure comment_count is set, default to 0 if not
        $comment_count = $first_post['comment_count'] ?? 0;
    ?>
    <div style="height:200px; background-image:url('<?= BASE_URL ?><?= htmlspecialchars($first_post['cover_img'] ?: '/assets/img/blog/default.webp') ?>')" class="latest-large set-bg">
        <div class="ll-text" style="background-image:linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(0, 0, 0, 0.5));">
            <h5><a href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= urlencode($first_post['slug']) ?>"><?= htmlspecialchars($first_post['title']) ?></a></h5>
            <ul>
                <li style="color:azure"><?= date('M d, Y', strtotime($first_post['created_at'])) ?></li>
                <li style="color:azure"><?= $comment_count ?> Comment<?= ($comment_count != 1 ? 's' : '') ?></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php foreach ($remaining_posts as $post):
        // Ensure comment_count is set, default to 0 if not
        $comment_count_loop = $post['comment_count'] ?? 0;
    ?>
    <div class="latest-item">
        <div class="li-pic">
            <img width="105" height="72" src="<?= BASE_URL ?><?= htmlspecialchars($post['cover_img'] ?: '/assets/img/blog/default.webp') ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>
        <div class="li-text">
            <h6><a href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h6>
            <span class="li-time"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
            <span class="li-comment"><i class="fa fa-comments"></i> <?= $comment_count_loop ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php

} catch (Throwable $e) {
    // In a production environment, log this error and show a generic message.
    echo "<p class=\"text-danger\">Error loading latest posts content.</p>";
    echo "<pre class=\"text-danger\">" . htmlspecialchars($e->getMessage()) . "</pre>";
}

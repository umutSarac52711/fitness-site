<?php
// ajax/latest-posts.php  –  returns latest 5 posts as mini-cards
require_once '../config.php';
// require_once BASE_PATH . '/includes/functions.php'; // Already included via config.php
header('Content-Type: text/html; charset=utf-8');

try {
    // Check if $pdo is available and a PDO instance
    if (!isset($pdo) || !$pdo instanceof PDO) {
        echo '<p class="text-danger"><strong>Debug:</strong> Database connection ($pdo) is not available or not a valid PDO object in ajax/latest-posts.php.</p>';
        exit;
    }

    // Direct count check
    try {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM posts");
        $postCount = $countStmt->fetchColumn();
        echo '<p class="small text-info"><strong>Debug (ajax/latest-posts.php):</strong> Direct count from `posts` table: ' . $postCount . '</p>';
    } catch (PDOException $e) {
        echo '<p class="text-danger"><strong>Debug (ajax/latest-posts.php):</strong> Error directly counting posts: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }

    $result_array = get_latest_posts(5);
    $latest = $result_array['data'] ?? [];
    $debug_messages = $result_array['debug'] ?? [];

    if (!empty($debug_messages)) {
        echo '<div class="alert alert-warning" style="font-size: 0.8em; max-height: 250px; overflow-y: auto; word-wrap: break-word;">';
        echo '<strong>Debug Info from get_latest_posts():</strong><br>';
        foreach ($debug_messages as $msg) {
            echo $msg . "<br>";
        }
        echo '</div>';
    }

    if ($latest === []) {
        echo '<p class="small text-muted">No posts found by get_latest_posts() (data array is empty).</p>';
        // The var_dump and pdo->errorInfo check can be removed if the above debug is sufficient
        // echo '<p class="small text-info"><strong>Debug:</strong> var_dump of get_latest_posts(5) data part:</p>';
        // echo '<pre style="font-size: 0.8em; background: #f0f0f0; padding: 5px;">';
        // var_dump($latest);
        // echo '</pre>';
        exit;
    } elseif (!is_array($latest)) { 
        echo '<p class="small text-danger"><strong>Error:</strong> get_latest_posts() did not return an array in the \'data\'.</p>';
        exit;
    }

    foreach ($latest as $post): ?>
      <div class="block-21 mb-4 d-flex">
        <a class="blog-img mr-4"
           href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= urlencode($post['slug']) ?>"
           style="background-image:url('<?= BASE_URL ?>/assets/img/blog/<?= htmlspecialchars($post['cover_img'] ?: 'blog_1.jpg') ?>');">
        </a>
        <div class="text">
          <h4 class="heading-1 mb-1">
            <a href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= urlencode($post['slug']) ?>">
              <?= htmlspecialchars($post['title']) ?>
            </a>
          </h4>
          <div class="meta">
            <div><span class="icon-calendar"></span>
              <?= date('d M Y', strtotime($post['created_at'])) ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach;

} catch (Throwable $e) { // Catch any throwable, including PDOException and Error
    // Ensure errors are displayed for debugging purposes when accessed via AJAX
    // In a production environment, you might log this error and show a generic message.
    echo "<p class=\"text-danger\">Error loading latest posts:</p>";
    echo "<pre class=\"text-danger\">" . htmlspecialchars($e->getMessage()) . "</pre>";
    // Optionally, include more details for debugging if not in production:
    // echo "<pre class=\\"text-danger\\">" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

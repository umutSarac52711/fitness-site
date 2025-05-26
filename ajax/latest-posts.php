<?php
// ajax/latest-posts.php  –  returns latest 5 posts as mini-cards
require_once '../config.php';
require_once BASE_PATH . '/includes/functions.php';
header('Content-Type: text/html; charset=utf-8');

$latest = get_latest_posts(5);

if (!$latest) {
    echo '<p class="small text-muted">Henüz yazı eklenmedi.</p>';
    exit;
}

foreach ($latest as $post): ?>
  <div class="block-21 mb-4 d-flex">
    <a class="blog-img mr-4"
       href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= urlencode($post['slug']) ?>"
       style="background-image:url(\'<?= BASE_URL ?>/assets/img/blog/<?= $post['thumbnail'] ?: 'default.jpg' ?>\');">
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

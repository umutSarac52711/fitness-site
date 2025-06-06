<?php
require_once __DIR__ . '/../includes/functions.php';
$latest = get_latest_posts(5);
?>

<div class="sidebar-box pt-3">
  <h3 class="heading">Latest Blog</h3>

  <div id="latest-posts-container">
    <?php if ($latest): ?>
      <?php foreach ($latest as $post): ?>
        <div class="block-21 mb-4 d-flex">
          <a class="blog-img mr-4"
             href="<?= BASE_URL ?>/pages/blog/blog-details.php?slug=<?= urlencode($post['slug']) ?>"
             style="background-image:url('<?= BASE_URL ?>/assets/img/blog/<?= $post['thumbnail'] ?: 'default.jpg' ?>');">
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
      <?php endforeach; ?>
    <?php else: ?>
      <p class="small text-muted">Henüz yazı eklenmedi.</p>
    <?php endif; ?>
  </div> 
</div>


<script>
(function () {
  const wrap = document.getElementById('latest-posts-container');
  if (!wrap) return;

  async function reloadLatest() {
    try {
      const res = await fetch('<?= BASE_URL ?>/ajax/latest-posts.php');
      if (!res.ok) throw new Error(res.status);
      wrap.innerHTML = await res.text();
    } catch (err) {
      console.warn('Latest posts reload failed:', err);
    }
  }

  reloadLatest();                 
  setInterval(reloadLatest, 30000); 
})();
</script>

(function () {
  const wrap = document.getElementById('latest-posts-container');
  if (!wrap) return;

  async function reloadLatest() {
    try {
      const res = await fetch(`${BASE_URL}/ajax/latest-posts.php`); // Use the JS BASE_URL
      if (!res.ok) throw new Error(res.status);
      wrap.innerHTML = await res.text();
    } catch (err) {
      console.warn('Latest posts reload failed:', err);
    }
  }

  reloadLatest();                 
  setInterval(reloadLatest, 30000); 
})();
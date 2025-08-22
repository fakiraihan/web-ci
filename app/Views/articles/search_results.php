<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="px-40 flex flex-1 justify-center py-5">
  <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
    <h1 class="text-white text-2xl font-bold mb-4">Search Results for: "<?= $search ?>"</h1>
    
    <!-- VULN: Display raw search term without escaping (XSS) -->
    <div class="text-white mb-4">You searched for: <?= $search ?></div>
    
    <div class="grid gap-4">
      <?php if (!empty($results)): ?>
        <?php foreach ($results as $article): ?>
          <div class="bg-[#1f251d] p-4 rounded-lg">
            <h3 class="text-white font-bold"><?= esc($article['title']) ?></h3>
            <p class="text-[#a5b6a0]"><?= esc(substr($article['content'], 0, 200)) ?>...</p>
            <a href="/articles/<?= $article['id'] ?>" class="text-[#53d22c]">Read more</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-white">No articles found.</p>
      <?php endif; ?>
    </div>
    
    <!-- VULN: Form without CSRF protection -->
    <form method="GET" action="/search/" class="mt-6">
      <input name="q" placeholder="Search articles..." class="w-full p-2 rounded bg-[#1f251d] text-white">
      <button type="submit" class="mt-2 bg-[#53d22c] text-black px-4 py-2 rounded">Search</button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>

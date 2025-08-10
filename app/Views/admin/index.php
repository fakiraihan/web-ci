<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#2d372a] px-10 py-3">
  <div class="flex items-center gap-4 text-white">
    <div class="size-4">
      <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill="currentColor"></path>
      </svg>
    </div>
    <h2 class="text-white text-lg font-bold leading-tight tracking-[-0.015em]">Admin</h2>
  </div>
  <div class="flex flex-1 justify-end gap-8">
    <div class="flex items-center gap-9">
      <a class="text-white text-sm font-medium leading-normal" href="<?= base_url('/admin') ?>">Manage Articles</a>
      <a class="text-white text-sm font-medium leading-normal" href="<?= base_url('/articles') ?>">View Website</a>
    </div>
    <div class="flex gap-2">
      <a href="<?= base_url('/admin/create') ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#53d22c] text-[#131712] text-sm font-bold leading-normal tracking-[0.015em]">
        <span class="truncate">Add New Article</span>
      </a>
      <a href="<?= base_url('/logout') ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#2d372a] text-white text-sm font-bold leading-normal tracking-[0.015em]">
        <span class="truncate">Logout</span>
      </a>
    </div>
  </div>
</header>
<div class="px-40 flex flex-1 justify-center py-5">
  <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
    <div class="flex flex-wrap justify-between gap-3 p-4">
      <p class="text-white tracking-light text-[32px] font-bold leading-tight min-w-72">All Articles</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="mx-4 mb-4 p-4 bg-green-600/20 border border-green-600/50 rounded-xl">
        <p class="text-green-300 text-sm"><?= session()->getFlashdata('success') ?></p>
      </div>
    <?php endif; ?>

    <div class="px-4 py-3 @container">
      <div class="flex overflow-hidden rounded-xl border border-[#42513e] bg-[#131712]">
        <table class="flex-1">
          <thead>
            <tr class="bg-[#1f251d]">
              <th class="px-4 py-3 text-left text-white w-[400px] text-sm font-medium leading-normal">Title</th>
              <th class="px-4 py-3 text-left text-white w-[400px] text-sm font-medium leading-normal">Author</th>
              <th class="px-4 py-3 text-left text-white w-[400px] text-sm font-medium leading-normal">Date</th>
              <th class="px-4 py-3 text-left text-white w-60 text-sm font-medium leading-normal">Status</th>
              <th class="px-4 py-3 text-left text-white w-60 text-[#a5b6a0] text-sm font-medium leading-normal">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($articles)): ?>
            <tr class="border-t border-t-[#42513e]">
              <td colspan="5" class="h-[72px] px-4 py-2 text-center text-[#a5b6a0] text-sm">
                No articles found. <a href="<?= base_url('/admin/create') ?>" class="text-[#53d22c] hover:underline">Create your first article</a>
              </td>
            </tr>
            <?php else: ?>
              <?php foreach ($articles as $article): ?>
              <tr class="border-t border-t-[#42513e]">
                <td class="h-[72px] px-4 py-2 w-[400px] text-white text-sm font-normal leading-normal">
                  <a href="<?= base_url('/articles/' . $article['id']) ?>" class="hover:text-[#53d22c]">
                    <?= esc($article['title']) ?>
                  </a>
                </td>
                <td class="h-[72px] px-4 py-2 w-[400px] text-[#a5b6a0] text-sm font-normal leading-normal">
                  <?= esc($article['author_name']) ?>
                </td>
                <td class="h-[72px] px-4 py-2 w-[400px] text-[#a5b6a0] text-sm font-normal leading-normal">
                  <?= date('M d, Y', strtotime($article['created_at'])) ?>
                </td>
                <td class="h-[72px] px-4 py-2 w-60">
                  <?php if ($article['status'] === 'published'): ?>
                    <div class="flex size-6 items-center justify-center rounded bg-[#53d22c]">
                      <div class="text-[#131712]" data-icon="Check" data-size="16px" data-weight="bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="currentColor" viewBox="0 0 256 256">
                          <path d="M228.49,76.49l-128,128a12,12,0,0,1-17,0l-56-56a12,12,0,0,1,17-17L96,183,211.51,59.51a12,12,0,0,1,17,17Z"></path>
                        </svg>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="flex size-6 items-center justify-center rounded bg-[#a5b6a0]">
                      <div class="text-[#131712]" data-icon="Clock" data-size="16px" data-weight="bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="currentColor" viewBox="0 0 256 256">
                          <path d="M128,28A100,100,0,1,0,228,128,100.11,100.11,0,0,0,128,28Zm0,176a76,76,0,1,1,76-76A76.08,76.08,0,0,1,128,204Zm44-76a12,12,0,0,1-12,12H128a12,12,0,0,1-12-12V72a12,12,0,0,1,24,0v44h28A12,12,0,0,1,172,128Z"></path>
                        </svg>
                      </div>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="h-[72px] px-4 py-2 w-60">
                  <div class="flex gap-2">
                    <a href="<?= base_url('/admin/edit/' . $article['id']) ?>" class="flex size-8 items-center justify-center rounded bg-[#2d372a] text-white hover:bg-[#53d22c] hover:text-[#131712] transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M227.31,73.37,182.63,28.69a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96A16,16,0,0,0,227.31,73.37ZM92.69,208H48V163.31l88-88L180.69,120ZM192,108.69,147.31,64l24-24L216,84.69Z"></path>
                      </svg>
                    </a>
                    <a href="<?= base_url('/admin/delete/' . $article['id']) ?>" onclick="return confirm('Are you sure you want to delete this article?')" class="flex size-8 items-center justify-center rounded bg-[#2d372a] text-white hover:bg-red-600 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                      </svg>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

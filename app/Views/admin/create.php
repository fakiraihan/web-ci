<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-8">
    <h1 class="text-4xl font-bold text-white">Create New Article</h1>
    <a href="<?= base_url('/admin') ?>" 
       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-md font-medium transition duration-200">
        Back to Dashboard
    </a>
</div>

<div class="bg-dark-card rounded-lg shadow-xl p-8 border border-dark-border">
    <form action="<?= base_url('/admin/store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Title *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="<?= old('title') ?>"
                       class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Enter article title" 
                       required>
                <?php if (isset($errors['title'])): ?>
                    <p class="text-red-400 text-sm mt-1"><?= $errors['title'] ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Category *</label>
                <select id="category" 
                        name="category" 
                        class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        required>
                    <option value="">Select a category</option>
                    <option value="technology" <?= old('category') === 'technology' ? 'selected' : '' ?>>Technology</option>
                    <option value="web-development" <?= old('category') === 'web-development' ? 'selected' : '' ?>>Web Development</option>
                    <option value="programming" <?= old('category') === 'programming' ? 'selected' : '' ?>>Programming</option>
                    <option value="tutorials" <?= old('category') === 'tutorials' ? 'selected' : '' ?>>Tutorials</option>
                </select>
                <?php if (isset($errors['category'])): ?>
                    <p class="text-red-400 text-sm mt-1"><?= $errors['category'] ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="author_name" class="block text-sm font-medium text-gray-300 mb-2">Author Name *</label>
                <input type="text" 
                       id="author_name" 
                       name="author_name" 
                       value="<?= old('author_name') ?>"
                       class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Enter author name" 
                       required>
                <?php if (isset($errors['author_name'])): ?>
                    <p class="text-red-400 text-sm mt-1"><?= $errors['author_name'] ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status *</label>
                <select id="status" 
                        name="status" 
                        class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        required>
                    <option value="published" <?= old('status') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= old('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
                <?php if (isset($errors['status'])): ?>
                    <p class="text-red-400 text-sm mt-1"><?= $errors['status'] ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div>
            <label for="image_url" class="block text-sm font-medium text-gray-300 mb-2">Featured Image URL</label>
            <input type="url" 
                   id="image_url" 
                   name="image_url" 
                   value="<?= old('image_url') ?>"
                   class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="https://example.com/image.jpg">
        </div>
        
        <div>
            <label for="author_image" class="block text-sm font-medium text-gray-300 mb-2">Author Image URL</label>
            <input type="url" 
                   id="author_image" 
                   name="author_image" 
                   value="<?= old('author_image') ?>"
                   class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="https://example.com/author.jpg">
        </div>
        
        <div>
            <label for="content" class="block text-sm font-medium text-gray-300 mb-2">Content *</label>
            <textarea id="content" 
                      name="content" 
                      rows="12" 
                      class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                      placeholder="Write your article content here..." 
                      required><?= old('content') ?></textarea>
            <?php if (isset($errors['content'])): ?>
                <p class="text-red-400 text-sm mt-1"><?= $errors['content'] ?></p>
            <?php endif; ?>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="<?= base_url('/admin') ?>" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-md font-medium transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition duration-200">
                Create Article
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

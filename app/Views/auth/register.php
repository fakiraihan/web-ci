<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="max-w-md mx-auto bg-dark-card rounded-lg shadow-xl p-8 border border-dark-border">
    <h2 class="text-3xl font-bold text-center mb-8 text-white">Register</h2>
    
    <form action="<?= base_url('/register') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>
        
        <div>
            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="<?= old('username') ?>"
                   class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="Choose a username" 
                   required>
            <?php if (isset($errors['username'])): ?>
                <p class="text-red-400 text-sm mt-1"><?= $errors['username'] ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="Choose a password" 
                   required>
            <?php if (isset($errors['password'])): ?>
                <p class="text-red-400 text-sm mt-1"><?= $errors['password'] ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <label for="password_confirm" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
            <input type="password" 
                   id="password_confirm" 
                   name="password_confirm" 
                   class="w-full px-3 py-2 bg-dark-bg border border-dark-border rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="Confirm your password" 
                   required>
            <?php if (isset($errors['password_confirm'])): ?>
                <p class="text-red-400 text-sm mt-1"><?= $errors['password_confirm'] ?></p>
            <?php endif; ?>
        </div>
        
        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-dark-card">
            Register
        </button>
    </form>
    
    <div class="mt-6 text-center">
        <p class="text-gray-400">Already have an account? 
            <a href="<?= base_url('/login') ?>" class="text-blue-400 hover:text-blue-300 font-medium">Login here</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>

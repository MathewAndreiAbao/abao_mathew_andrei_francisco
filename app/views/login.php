<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Notes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center text-gray-200">
    <div class="bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold mb-6 text-center">Login</h1>

        <?php $LAVA = lava_instance(); ?>
        <?= $LAVA->form_validation->errors(); ?>

        <?php if (isset($error)): ?>
            <div class="mb-4 bg-red-800 border border-red-600 text-red-200 px-4 py-3 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('/login_post') ?>" method="post" class="space-y-4">
            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500" />
            </div>
            <div>
                <label for="password" class="block font-semibold mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500" />
            </div>
            <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 rounded transition">Login</button>
        </form>

        <p class="mt-4 text-center">
            Don't have an account?
            <a href="<?= site_url('/register') ?>" class="underline hover:text-gray-400">Register here</a>
        </p>
    </div>
</body>
</html>
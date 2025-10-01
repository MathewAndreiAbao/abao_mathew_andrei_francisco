<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Notes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center text-gray-200">
    <div class="bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold mb-6 text-center">Register</h1>

        <?php $LAVA = lava_instance(); ?>
        <?= $LAVA->form_validation->errors(); ?>

        <?php if (isset($error)): ?>
            <div class="mb-4 bg-red-800 border border-red-600 text-red-200 px-4 py-3 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($LAVA->session->flashdata('success')): ?>
            <div class="mb-4 bg-green-800 border border-green-600 text-green-200 px-4 py-3 rounded">
                <?= htmlspecialchars($LAVA->session->flashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('/register_post') ?>" method="post" class="space-y-4">
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
            <div>
                <label for="confirm_password" class="block font-semibold mb-1">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500" />
            </div>
            <div>
                <label for="role" class="block font-semibold mb-1">Role</label>
                <select id="role" name="role" class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <option value="user" <?= isset($role) && $role == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= isset($role) && $role == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 rounded transition">Register</button>
        </form>

        <p class="mt-4 text-center">
            Already have an account?
            <a href="<?= site_url('/login') ?>" class="underline hover:text-gray-400">Login here</a>
        </p>
    </div>
</body>
</html>
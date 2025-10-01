<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Note - Notes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center text-gray-200">
    <div class="bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold mb-6 text-center">Create Note</h1>

        <?php $LAVA = lava_instance(); ?>
        <?= $LAVA->form_validation->errors(); ?>

        <form action="<?= site_url('/notes/create_post') ?>" method="post" class="space-y-4">
            <div>
                <label for="title" class="block font-semibold mb-1">Title</label>
                <input type="text" id="title" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500" />
            </div>
            <div>
                <label for="content" class="block font-semibold mb-1">Content</label>
                <textarea id="content" name="content" rows="6" required
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500"><?= isset($content) ? htmlspecialchars($content) : '' ?></textarea>
            </div>
            <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 rounded transition">Create Note</button>
        </form>

        <p class="mt-4 text-center">
            <a href="<?= site_url('/notes') ?>" class="underline hover:text-gray-400">Back to Notes</a>
        </p>
    </div>
</body>
</html>
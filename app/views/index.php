<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Notes - Notes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen text-gray-200">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-4xl font-bold">Notes</h1>
            <div>
                <?php if ($role === 'admin'): ?>
                    <a href="<?= site_url('/notes/create') ?>" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition">Create Note</a>
                <?php endif; ?>
                <a href="<?= site_url('/logout') ?>" class="ml-4 bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded transition">Logout</a>
            </div>
        </div>

        <form method="get" action="<?= site_url('/notes') ?>" class="mb-6 flex gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search notes..." class="px-4 py-2 rounded flex-1 max-w-md bg-gray-800 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-gray-200" />
            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded transition">Search</button>
        </form>

        <?php $LAVA = lava_instance(); ?>
        <?php if ($LAVA->session->flashdata('success')): ?>
            <div class="mb-4 bg-green-800 border border-green-600 text-green-200 px-4 py-3 rounded">
                <?= htmlspecialchars($LAVA->session->flashdata('success')) ?>
            </div>
        <?php endif; ?>
        <?php if ($LAVA->session->flashdata('error')): ?>
            <div class="mb-4 bg-red-800 border border-red-600 text-red-200 px-4 py-3 rounded">
                <?= htmlspecialchars($LAVA->session->flashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($notes)): ?>
            <p>No notes found.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($notes as $note): ?>
                    <div class="bg-gray-800 rounded-lg p-4 shadow-md">
                        <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($note['title']) ?></h2>
                        <p class="mb-4"><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                        <p class="text-sm mb-1">Created: <?= htmlspecialchars($note['created_at']) ?></p>
                        <?php if ($role === 'admin'): ?>
                            <p class="text-sm mb-4">Created by: <?= htmlspecialchars($note['user_email']) ?></p>
                            <div class="flex justify-between">
                                <a href="<?= site_url('/notes/edit/' . $note['id']) ?>" class="text-gray-400 hover:underline">Edit</a>
                                <a href="<?= site_url('/notes/delete/' . $note['id']) ?>" onclick="return confirm('Are you sure you want to delete this note?');" class="text-red-400 hover:underline">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
            <div class="flex justify-center mt-6 space-x-2">
                <?php if ($pagination['current_page'] > 1): ?>
                    <a href="<?= site_url('/notes?page=' . ($pagination['current_page'] - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                    <a href="<?= site_url('/notes?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="px-3 py-2 rounded transition <?php echo $i == $pagination['current_page'] ? 'bg-gray-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white'; ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                    <a href="<?= site_url('/notes?page=' . ($pagination['current_page'] + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
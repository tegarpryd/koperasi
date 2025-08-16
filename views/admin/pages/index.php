<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Halaman Statis</h2>
        <a href="/admin/pages/create" class="btn btn-primary">Buat Halaman Baru</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Slug (URL)</th>
                        <th>Status</th>
                        <th>Terakhir Diperbarui</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($pages) && !empty($pages)): ?>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td><?= htmlspecialchars($page['title']) ?></td>
                                <td>/<?= htmlspecialchars($page['slug']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $page['is_published'] ? 'success' : 'secondary' ?>">
                                        <?= $page['is_published'] ? 'Diterbitkan' : 'Draft' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($page['updated_at']))) ?></td>
                                <td>
                                    <a href="/admin/pages/<?= $page['id'] ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="/admin/pages/<?= $page['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus halaman ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada halaman statis.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

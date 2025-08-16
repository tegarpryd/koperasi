<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Anggota</h2>
        <a href="/<?= htmlspecialchars($admin_path) ?>/members/create" class="btn btn-primary">Tambah Anggota Baru</a>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kode Anggota</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Tanggal Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($members) && !empty($members)): ?>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= htmlspecialchars($member['member_code']) ?></td>
                                <td><?= htmlspecialchars($member['full_name']) ?></td>
                                <td><?= htmlspecialchars($member['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $member['status'] === 'active' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($member['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($member['join_date']))) ?></td>
                                <td>
                                    <a href="/<?= htmlspecialchars($admin_path) ?>/members/<?= $member['id'] ?>/savings" class="btn btn-sm btn-info">Simpanan</a>
                                    <a href="/<?= htmlspecialchars($admin_path) ?>/members/edit/<?= $member['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="/<?= htmlspecialchars($admin_path) ?>/members/delete/<?= $member['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data anggota.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php require_once '../views/partials/pagination.php'; ?>

        </div>
    </div>
</div>

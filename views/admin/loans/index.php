<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manajemen Pinjaman</h2>
        <form action="/<?= htmlspecialchars($admin_path) ?>/loans/check-overdue" method="POST">
            <button type="submit" class="btn btn-outline-danger">Periksa & Terapkan Denda</button>
        </form>
    </div>

    <!-- Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="/<?= htmlspecialchars($admin_path) ?>/loans" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter berdasarkan Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                        <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Tgl Pengajuan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($loans) && !empty($loans)): ?>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= htmlspecialchars($loan['full_name']) ?> (<?= htmlspecialchars($loan['member_code']) ?>)</td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($loan['application_date']))) ?></td>
                                <td>Rp <?= number_format($loan['loan_amount'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($loan['status']) ?></span>
                                </td>
                                <td>
                                    <a href="/<?= htmlspecialchars($admin_path) ?>/loans/<?= $loan['id'] ?>" class="btn btn-sm btn-secondary">Lihat Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data pinjaman yang cocok.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php require_once '../views/partials/pagination.php'; ?>
        </div>
    </div>
</div>

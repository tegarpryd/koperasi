<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Riwayat Pinjaman Saya</h2>
        <a href="/dashboard/loans/apply" class="btn btn-primary">Ajukan Pinjaman Baru</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Tenor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($loans) && !empty($loans)): ?>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($loan['application_date']))) ?></td>
                                <td>Rp <?= number_format($loan['loan_amount'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($loan['tenor_months']) ?> bulan</td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($loan['status']) ?></span>
                                </td>
                                <td>
                                    <a href="/dashboard/loans/<?= $loan['id'] ?>" class="btn btn-sm btn-secondary">Lihat Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Anda belum memiliki riwayat pinjaman.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php require_once '../views/partials/pagination.php'; ?>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Pinjaman #<?= htmlspecialchars($loan['id']) ?></h2>
        <a href="/dashboard/loans" class="btn btn-secondary">Kembali ke Riwayat Pinjaman</a>
    </div>

    <!-- Info Pinjaman -->
    <div class="card mb-4">
        <div class="card-header">
            Informasi Pinjaman
        </div>
        <div class="card-body">
            <p><strong>Jumlah:</strong> Rp <?= number_format($loan['loan_amount'], 2, ',', '.') ?></p>
            <p><strong>Tenor:</strong> <?= htmlspecialchars($loan['tenor_months']) ?> bulan</p>
            <p><strong>Status:</strong> <span class="badge bg-info"><?= htmlspecialchars($loan['status']) ?></span></p>
        </div>
    </div>

    <!-- Jadwal Angsuran -->
    <div class="card">
        <div class="card-header">
            Jadwal Angsuran
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Angsuran Ke-</th>
                        <th>Jatuh Tempo</th>
                        <th>Jumlah Tagihan</th>
                        <th>Pokok</th>
                        <th>Bunga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($installments) && !empty($installments)): ?>
                        <?php foreach ($installments as $inst): ?>
                            <tr>
                                <td><?= htmlspecialchars($inst['installment_number']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($inst['due_date']))) ?></td>
                                <td>Rp <?= number_format($inst['amount_due'], 2, ',', '.') ?></td>
                                <td>Rp <?= number_format($inst['principal_amount'], 2, ',', '.') ?></td>
                                <td>Rp <?= number_format($inst['interest_amount'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $inst['status'] === 'paid' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($inst['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Jadwal angsuran belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

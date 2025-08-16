<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Pinjaman #<?= htmlspecialchars($loan['id']) ?></h2>
        <a href="/<?= htmlspecialchars($admin_path) ?>/loans" class="btn btn-secondary">Kembali ke Daftar Pinjaman</a>
    </div>

    <!-- Info Pinjaman -->
    <div class="card mb-4">
        <div class="card-header">
            Informasi Pinjaman & Anggota
        </div>
        <div class="card-body">
            <p><strong>Anggota:</strong> <?= htmlspecialchars($loan['full_name']) ?> (<?= htmlspecialchars($loan['member_code']) ?>)</p>
            <p><strong>Jumlah:</strong> Rp <?= number_format($loan['loan_amount'], 2, ',', '.') ?></p>
            <p><strong>Status:</strong> <span class="badge bg-info"><?= htmlspecialchars($loan['status']) ?></span></p>

            <?php if ($loan['status'] === 'pending'): ?>
                <hr>
                <form action="/<?= htmlspecialchars($admin_path) ?>/loans/<?= $loan['id'] ?>/approve" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-success">Setujui Pinjaman</button>
                </form>
                <form action="/<?= htmlspecialchars($admin_path) ?>/loans/<?= $loan['id'] ?>/reject" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-danger">Tolak Pinjaman</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form Pembayaran (jika disetujui) -->
    <?php if ($loan['status'] === 'approved'): ?>
    <div class="card mb-4">
        <div class="card-header">Catat Pembayaran Angsuran</div>
        <div class="card-body">
            <form action="/<?= htmlspecialchars($admin_path) ?>/loans/payment" method="POST">
                <input type="hidden" name="loan_id" value="<?= htmlspecialchars($loan['id']) ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label for="installment_id" class="form-label">Untuk Angsuran Ke-</label>
                        <select name="installment_id" id="installment_id" class="form-select">
                            <?php foreach($installments as $inst) { if($inst['status'] === 'pending') {
                                $value = htmlspecialchars($inst['id']);
                                $text = "Angsuran #" . htmlspecialchars($inst['installment_number']) . " (Jatuh Tempo: " . htmlspecialchars(date('d M Y', strtotime($inst['due_date']))) . ")";
                                echo "<option value='{$value}'>{$text}</option>";
                            } } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="payment_amount" class="form-label">Jumlah Pembayaran</label>
                        <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="form-control" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Jadwal Angsuran -->
    <div class="card">
        <div class="card-header">Jadwal Angsuran</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Angsuran Ke-</th>
                        <th>Jatuh Tempo</th>
                        <th>Jumlah Tagihan</th>
                        <th>Status</th>
                        <th>Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($installments) && !empty($installments)): ?>
                        <?php foreach ($installments as $inst): ?>
                            <tr>
                                <td><?= htmlspecialchars($inst['installment_number']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($inst['due_date']))) ?></td>
                                <td>Rp <?= number_format($inst['amount_due'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $inst['status'] === 'paid' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($inst['status']) ?>
                                    </span>
                                </td>
                                <td><?= $inst['payment_date'] ? htmlspecialchars(date('d M Y', strtotime($inst['payment_date']))) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Jadwal angsuran akan dibuat setelah pinjaman disetujui.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

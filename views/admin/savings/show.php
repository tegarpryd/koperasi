<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Simpanan: <?= htmlspecialchars($member['full_name'] ?? 'Anggota') ?></h2>
        <a href="/<?= htmlspecialchars($admin_path) ?>/members" class="btn btn-secondary">Kembali ke Daftar Anggota</a>
    </div>

    <!-- Bagian Ringkasan Saldo -->
    <h4>Ringkasan Saldo</h4>
    <div class="row mb-4">
        <?php
            $balances = [];
            foreach ($accounts as $account) {
                $balances[$account['saving_type']] = $account['balance'];
            }
        ?>
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Simpanan Pokok</h5>
                    <h2>Rp <?= number_format($balances['pokok'] ?? 0, 2, ',', '.') ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Simpanan Wajib</h5>
                    <h2>Rp <?= number_format($balances['wajib'] ?? 0, 2, ',', '.') ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Simpanan Sukarela</h5>
                    <h2>Rp <?= number_format($balances['sukarela'] ?? 0, 2, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Form Transaksi Baru -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Buat Transaksi Baru</h4>
        </div>
        <div class="card-body">
            <form action="/<?= htmlspecialchars($admin_path) ?>/savings/transaction" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="member_id" value="<?= htmlspecialchars($member['id']) ?>">
                <div class="row">
                    <div class="col-md-3">
                        <label for="amount" class="form-label">Jumlah</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="col-md-3">
                        <label for="transaction_type" class="form-label">Jenis Transaksi</label>
                        <select class="form-select" id="transaction_type" name="transaction_type">
                            <option value="deposit">Setoran</option>
                            <option value="withdrawal">Penarikan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="saving_type" class="form-label">Jenis Simpanan</label>
                        <select class="form-select" id="saving_type" name="saving_type">
                            <option value="pokok">Simpanan Pokok</option>
                            <option value="wajib">Simpanan Wajib</option>
                            <option value="sukarela">Simpanan Sukarela</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="description" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bagian Riwayat Transaksi -->
    <h4>Riwayat Transaksi</h4>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Tipe Simpanan</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($transactions) && !empty($transactions)): ?>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y H:i', strtotime($tx['transaction_date']))) ?></td>
                                <td>
                                    <span class="badge bg-<?= $tx['transaction_type'] === 'deposit' ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars(ucfirst($tx['transaction_type'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(ucfirst($tx['saving_type'])) ?></td>
                                <td>Rp <?= number_format($tx['amount'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($tx['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada riwayat transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php require_once '../views/partials/pagination.php'; ?>

        </div>
    </div>
</div>

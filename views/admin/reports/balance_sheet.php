<div class="container mt-4">
    <h2>Laporan Neraca (Sederhana)</h2>
    <p class="text-muted">Data per tanggal: <?= date('d M Y') ?></p>

    <div class="row">
        <div class="col-md-8">
            <h4>Posisi Keuangan</h4>
            <table class="table table-bordered">
                <thead>
                    <tr class="table-light">
                        <th colspan="2">Aset</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Kas</td>
                        <td class="text-end">Rp <?= number_format($balanceSheetData['assets']['cash'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Piutang Pinjaman</td>
                        <td class="text-end">Rp <?= number_format($balanceSheetData['assets']['receivables'], 2, ',', '.') ?></td>
                    </tr>
                    <tr class="table-active">
                        <td><strong>Total Aset</strong></td>
                        <td class="text-end"><strong>Rp <?= number_format($balanceSheetData['assets']['total'], 2, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
                <thead>
                    <tr class="table-light">
                        <th colspan="2">Kewajiban dan Ekuitas</th>
                    </tr>
                </thead>
                <tbody>
                     <tr>
                        <td>Simpanan Anggota</td>
                        <td class="text-end">Rp <?= number_format($balanceSheetData['liabilities']['member_savings'], 2, ',', '.') ?></td>
                    </tr>
                    <tr class="table-active">
                        <td><strong>Total Kewajiban</strong></td>
                        <td class="text-end"><strong>Rp <?= number_format($balanceSheetData['liabilities']['total'], 2, ',', '.') ?></strong></td>
                    </tr>
                     <tr>
                        <td>Ekuitas (Modal)</td>
                        <td class="text-end">Rp <?= number_format($balanceSheetData['equity'], 2, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <h4>Komposisi Aset</h4>
            <canvas id="assetsChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('assetsChart').getContext('2d');

    const assetsData = <?= json_encode($balanceSheetData['assets'] ?? []) ?>;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Kas', 'Piutang Pinjaman'],
            datasets: [{
                label: 'Komposisi Aset',
                data: [assetsData.cash, assetsData.receivables],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
});
</script>

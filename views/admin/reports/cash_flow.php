<div class="container mt-4">
    <h2>Laporan Arus Kas</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/<?= htmlspecialchars($admin_path) ?>/reports/cash-flow" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? date('Y-m-01')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <canvas id="cashFlowChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('cashFlowChart').getContext('2d');

    // Data dari controller PHP di-encode sebagai JSON
    const cashFlowData = <?= json_encode($cashFlowData ?? []) ?>;

    const labels = cashFlowData.map(item => item.date);
    const dataIn = cashFlowData.map(item => item.total_in);
    const dataOut = cashFlowData.map(item => item.total_out);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Uang Masuk',
                    data: dataIn,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                },
                {
                    label: 'Uang Keluar',
                    data: dataOut,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<div class="container mt-4">
    <h2>Laporan Aktivitas Anggota (Pendaftar Baru)</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/<?= htmlspecialchars($admin_path) ?>/reports/member-activity" class="row g-3">
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
            <canvas id="memberActivityChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('memberActivityChart').getContext('2d');

    const activityData = <?= json_encode($memberActivityData ?? []) ?>;

    const labels = activityData.map(item => item.date);
    const data = activityData.map(item => item.new_members);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Jumlah Anggota Baru per Hari',
                    data: data,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

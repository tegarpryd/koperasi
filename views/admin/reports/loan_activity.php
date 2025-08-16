<div class="container mt-4">
    <h2>Laporan Aktivitas Pinjaman (Berdasarkan Status)</h2>

    <div class="card">
        <div class="card-body" style="width: 50%; margin: auto;">
            <canvas id="loanActivityChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('loanActivityChart').getContext('2d');

    const activityData = <?= json_encode($loanActivityData ?? []) ?>;

    const labels = activityData.map(item => item.status);
    const data = activityData.map(item => item.count);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pinjaman',
                data: data,
                backgroundColor: [
                    'rgba(255, 206, 86, 0.6)', // pending
                    'rgba(75, 192, 192, 0.6)', // approved
                    'rgba(255, 99, 132, 0.6)', // rejected
                    'rgba(54, 162, 235, 0.6)', // completed
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                ],
                borderWidth: 1
            }]
        }
    });
});
</script>

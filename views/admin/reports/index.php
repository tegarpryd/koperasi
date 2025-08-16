<div class="container mt-4">
    <h2>Laporan & Analitik</h2>
    <p>Pilih laporan yang ingin Anda lihat dari daftar di bawah ini.</p>

    <div class="list-group">
        <a href="/<?= htmlspecialchars($admin_path) ?>/reports/cash-flow" class="list-group-item list-group-item-action">
            Laporan Arus Kas (Cash Flow)
        </a>
        <a href="/<?= htmlspecialchars($admin_path) ?>/reports/profit-loss" class="list-group-item list-group-item-action">Laporan Laba Rugi</a>
        <a href="/<?= htmlspecialchars($admin_path) ?>/reports/balance-sheet" class="list-group-item list-group-item-action">Laporan Neraca</a>
        <a href="/<?= htmlspecialchars($admin_path) ?>/reports/member-activity" class="list-group-item list-group-item-action">Laporan Aktivitas Anggota</a>
        <a href="/<?= htmlspecialchars($admin_path) ?>/reports/loan-activity" class="list-group-item list-group-item-action">Laporan Aktivitas Pinjaman</a>
    </div>
</div>

<div class="container mt-5">
    <h1>Selamat Datang di Dashboard Admin</h1>
    <p>Halaman ini hanya dapat diakses oleh pengguna dengan peran 'admin'.</p>
    <p>Email Anda: <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
    <p>Peran Anda: <?= htmlspecialchars($_SESSION['user']['role']) ?></p>
    <hr>
    <p>Di sini Anda akan dapat mengelola anggota, simpanan, pinjaman, dan melihat laporan keuangan.</p>
    <a href="/logout" class="btn btn-danger">Logout</a>
</div>

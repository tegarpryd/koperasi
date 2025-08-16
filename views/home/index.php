<style>
    .hero {
        background: #f8f9fa;
        padding: 4rem 2rem;
        border-radius: 0.5rem;
    }
    .feature-icon {
        font-size: 3rem;
        color: #0d6efd; /* This will be dynamic later */
    }
</style>

<div class="container">
    <!-- Hero Section -->
    <div class="hero text-center my-5">
        <h1 class="display-4"><?= htmlspecialchars($settings['landing_hero_title'] ?? 'Solusi Simpan Pinjam Modern') ?></h1>
        <p class="lead"><?= htmlspecialchars($settings['landing_hero_subtitle'] ?? 'Kelola keuangan koperasi Anda dengan mudah, aman, dan efisien.') ?></p>
        <a href="/register" class="btn btn-primary btn-lg">Daftar Sekarang</a>
        <a href="/login" class="btn btn-secondary btn-lg">Login Anggota</a>
    </div>

    <!-- Features Section -->
    <div class="row text-center my-5">
        <div class="col-md-4">
            <div class="feature-icon mb-3">✓</div>
            <h3><?= htmlspecialchars($settings['landing_feature1_title'] ?? 'Manajemen Anggota') ?></h3>
            <p><?= htmlspecialchars($settings['landing_feature1_text'] ?? 'Kelola data anggota, status keanggotaan, dan profil dengan mudah.') ?></p>
        </div>
        <div class="col-md-4">
            <div class="feature-icon mb-3">💰</div>
            <h3><?= htmlspecialchars($settings['landing_feature2_title'] ?? 'Simpanan & Pinjaman') ?></h3>
            <p><?= htmlspecialchars($settings['landing_feature2_text'] ?? 'Sistem pencatatan simpanan yang fleksibel dan alur pinjaman yang terotomatisasi.') ?></p>
        </div>
        <div class="col-md-4">
            <div class="feature-icon mb-3">📊</div>
            <h3><?= htmlspecialchars($settings['landing_feature3_title'] ?? 'Laporan Lengkap') ?></h3>
            <p><?= htmlspecialchars($settings['landing_feature3_text'] ?? 'Dapatkan wawasan mendalam dengan laporan keuangan yang disajikan secara visual.') ?></p>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="text-center my-5">
        <h2><?= htmlspecialchars($settings['landing_cta_title'] ?? 'Siap untuk Memulai?') ?></h2>
        <p><?= htmlspecialchars($settings['landing_cta_subtitle'] ?? 'Transformasikan cara Anda mengelola keuangan koperasi hari ini.') ?></p>
        <a href="/register" class="btn btn-primary btn-lg">Gabung Sekarang</a>
    </div>
</div>

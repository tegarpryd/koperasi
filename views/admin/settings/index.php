<div class="container mt-4">
    <h2>Pengaturan Situs</h2>
    <p>Ubah konfigurasi umum untuk seluruh aplikasi di sini.</p>

    <div class="card">
        <div class="card-body">
            <form action="/admin/settings" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                <h4>Informasi Perusahaan</h4>
                <div class="mb-3">
                    <label for="company_name" class="form-label">Nama Perusahaan/Koperasi</label>
                    <input type="text" class="form-control" id="company_name" name="settings[company_name]" value="<?= htmlspecialchars($settings['company_name'] ?? 'Koperasi Maju Jaya') ?>">
                </div>
                <div class="mb-3">
                    <label for="company_address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="company_address" name="settings[company_address]"><?= htmlspecialchars($settings['company_address'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="company_contact" class="form-label">Kontak (Telepon/Email)</label>
                    <input type="text" class="form-control" id="company_contact" name="settings[company_contact]" value="<?= htmlspecialchars($settings['company_contact'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="logo_path" class="form-label">Path Logo</label>
                    <input type="text" class="form-control" id="logo_path" name="settings[logo_path]" value="<?= htmlspecialchars($settings['logo_path'] ?? '/assets/img/logo.png') ?>">
                    <div class="form-text">Path ke file logo di dalam direktori 'public'.</div>
                </div>

                <hr>

                <hr>

                <h4>Konten Landing Page</h4>
                <div class="mb-3">
                    <label for="landing_hero_title" class="form-label">Judul Hero</label>
                    <input type="text" class="form-control" id="landing_hero_title" name="settings[landing_hero_title]" value="<?= htmlspecialchars($settings['landing_hero_title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="landing_hero_subtitle" class="form-label">Sub-judul Hero</label>
                    <textarea class="form-control" id="landing_hero_subtitle" name="settings[landing_hero_subtitle]"><?= htmlspecialchars($settings['landing_hero_subtitle'] ?? '') ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="landing_feature1_title" class="form-label">Judul Fitur 1</label>
                        <input type="text" class="form-control" id="landing_feature1_title" name="settings[landing_feature1_title]" value="<?= htmlspecialchars($settings['landing_feature1_title'] ?? '') ?>">
                        <label for="landing_feature1_text" class="form-label mt-2">Teks Fitur 1</label>
                        <textarea class="form-control" id="landing_feature1_text" name="settings[landing_feature1_text]"><?= htmlspecialchars($settings['landing_feature1_text'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="landing_feature2_title" class="form-label">Judul Fitur 2</label>
                        <input type="text" class="form-control" id="landing_feature2_title" name="settings[landing_feature2_title]" value="<?= htmlspecialchars($settings['landing_feature2_title'] ?? '') ?>">
                        <label for="landing_feature2_text" class="form-label mt-2">Teks Fitur 2</label>
                        <textarea class="form-control" id="landing_feature2_text" name="settings[landing_feature2_text]"><?= htmlspecialchars($settings['landing_feature2_text'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="landing_feature3_title" class="form-label">Judul Fitur 3</label>
                        <input type="text" class="form-control" id="landing_feature3_title" name="settings[landing_feature3_title]" value="<?= htmlspecialchars($settings['landing_feature3_title'] ?? '') ?>">
                         <label for="landing_feature3_text" class="form-label mt-2">Teks Fitur 3</label>
                        <textarea class="form-control" id="landing_feature3_text" name="settings[landing_feature3_text]"><?= htmlspecialchars($settings['landing_feature3_text'] ?? '') ?></textarea>
                    </div>
                </div>
                 <div class="mb-3 mt-3">
                    <label for="landing_cta_title" class="form-label">Judul CTA</label>
                    <input type="text" class="form-control" id="landing_cta_title" name="settings[landing_cta_title]" value="<?= htmlspecialchars($settings['landing_cta_title'] ?? '') ?>">
                </div>
                 <div class="mb-3">
                    <label for="landing_cta_subtitle" class="form-label">Sub-judul CTA</label>
                    <input type="text" class="form-control" id="landing_cta_subtitle" name="settings[landing_cta_subtitle]" value="<?= htmlspecialchars($settings['landing_cta_subtitle'] ?? '') ?>">
                </div>


                <hr>

                <hr>

                <h4>Pengaturan Tema</h4>
                <div class="row">
                    <div class="col-md-4">
                        <label for="theme_primary_color" class="form-label">Warna Primer</label>
                        <input type="color" class="form-control form-control-color" id="theme_primary_color" name="settings[theme_primary_color]" value="<?= htmlspecialchars($settings['theme_primary_color'] ?? '#0d6efd') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="theme_secondary_color" class="form-label">Warna Sekunder</label>
                        <input type="color" class="form-control form-control-color" id="theme_secondary_color" name="settings[theme_secondary_color]" value="<?= htmlspecialchars($settings['theme_secondary_color'] ?? '#6c757d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="theme_text_color" class="form-label">Warna Teks Utama</label>
                        <input type="color" class="form-control form-control-color" id="theme_text_color" name="settings[theme_text_color]" value="<?= htmlspecialchars($settings['theme_text_color'] ?? '#212529') ?>">
                    </div>
                </div>

                <hr>

                <h4>Konfigurasi Lanjutan</h4>
                 <div class="mb-3">
                    <label for="admin_path" class="form-label">Path Admin</label>
                    <input type="text" class="form-control" id="admin_path" name="settings[admin_path]" value="<?= htmlspecialchars($settings['admin_path'] ?? 'admin') ?>">
                    <div class="form-text">
                        <strong>Peringatan:</strong> Mengubah ini akan mengubah URL untuk semua halaman admin (misal: /admin menjadi /panel).
                        Pastikan untuk mengingat path baru Anda.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </form>
        </div>
    </div>
</div>

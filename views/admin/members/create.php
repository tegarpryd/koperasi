<div class="container mt-4">
    <h2>Tambah Anggota Baru</h2>

    <div class="card">
        <div class="card-body">
            <form action="/<?= htmlspecialchars($admin_path) ?>/members" method="POST">
                <h4>Informasi Pribadi</h4>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                </div>
                <div class="mb-3">
                    <label for="join_date" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="join_date" name="join_date" required>
                </div>

                <hr>

                <h4>Informasi Akun</h4>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Password default untuk anggota baru. Anggota dapat mengubahnya nanti.</div>
                </div>

                <a href="/<?= htmlspecialchars($admin_path) ?>/members" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Anggota</button>
            </form>
        </div>
    </div>
</div>

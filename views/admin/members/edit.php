<div class="container mt-4">
    <h2>Edit Data Anggota</h2>

    <div class="card">
        <div class="card-body">
            <form action="/<?= htmlspecialchars($admin_path) ?>/members/update/<?= htmlspecialchars($member['id']) ?>" method="POST">
                <h4>Informasi Pribadi</h4>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($member['full_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($member['address']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($member['phone_number']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="join_date" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="join_date" name="join_date" value="<?= htmlspecialchars($member['join_date']) ?>" required>
                </div>

                <hr>

                <h4>Informasi Akun</h4>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($member['email']) ?>" readonly>
                    <div class="form-text">Email tidak dapat diubah.</div>
                </div>
                 <div class="mb-3">
                    <label for="status" class="form-label">Status Keanggotaan</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                </div>


                <a href="/<?= htmlspecialchars($admin_path) ?>/members" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </form>
        </div>
    </div>
</div>

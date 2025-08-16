<div class="container mt-4">
    <h2>Buat Halaman Statis Baru</h2>

    <div class="card">
        <div class="card-body">
            <form action="/admin/pages" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Judul Halaman</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (URL)</label>
                    <input type="text" class="form-control" id="slug" name="slug" required>
                    <div class="form-text">Contoh: "kebijakan-privasi". Akan muncul sebagai /kebijakan-privasi.</div>
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">Isi Halaman (Body)</label>
                    <textarea class="form-control" id="body" name="body" rows="15"></textarea>
                    <div class="form-text">Anda dapat menggunakan HTML di sini.</div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="is_published" name="is_published">
                    <label class="form-check-label" for="is_published">
                        Terbitkan halaman ini
                    </label>
                </div>
                <a href="/admin/pages" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Halaman</button>
            </form>
        </div>
    </div>
</div>

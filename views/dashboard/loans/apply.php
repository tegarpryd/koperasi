<div class="container mt-4">
    <h2>Formulir Pengajuan Pinjaman</h2>

    <div class="card">
        <div class="card-body">
            <form action="/dashboard/loans" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="mb-3">
                    <label for="loan_amount" class="form-label">Jumlah Pinjaman (Rp)</label>
                    <input type="number" class="form-control" id="loan_amount" name="loan_amount" required>
                </div>
                <div class="mb-3">
                    <label for="tenor_months" class="form-label">Tenor (Bulan)</label>
                    <input type="number" class="form-control" id="tenor_months" name="tenor_months" required>
                </div>
                 <div class="mb-3">
                    <label for="interest_rate_percent" class="form-label">Suku Bunga (% per tahun)</label>
                    <input type="number" step="0.01" class="form-control" id="interest_rate_percent" name="interest_rate_percent" value="12.00" readonly>
                    <div class="form-text">Suku bunga tetap per tahun (contoh).</div>
                </div>
                <div class="mb-3">
                    <label for="purpose" class="form-label">Tujuan Pinjaman</label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
                </div>

                <a href="/dashboard/loans" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Ajukan Pinjaman</button>
            </form>
        </div>
    </div>
</div>

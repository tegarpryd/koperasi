<?php

require_once 'Model.php';

class Loan extends Model
{
    protected $loans_table = 'loans';
    protected $installments_table = 'loan_installments';

    public function findLoansByMemberId($member_id, $limit, $offset) {
        $sql = "SELECT * FROM {$this->loans_table} WHERE member_id = :member_id ORDER BY application_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countLoansByMemberId($member_id) {
        $sql = "SELECT COUNT(*) FROM {$this->loans_table} WHERE member_id = :member_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':member_id' => $member_id]);
        return $stmt->fetchColumn();
    }

    public function findLoanDetails($loan_id) {
        $sql = "SELECT l.*, m.full_name, m.member_code, m.user_id
                FROM {$this->loans_table} l
                JOIN members m ON l.member_id = m.id
                WHERE l.id = :loan_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':loan_id' => $loan_id]);
        return $stmt->fetch();
    }

    public function findAllLoans(array $filters = [], $limit, $offset) {
        $sql = "SELECT l.*, m.full_name, m.member_code FROM {$this->loans_table} l JOIN members m ON l.member_id = m.id";
        if (!empty($filters['status'])) {
            $sql .= " WHERE l.status = :status";
        }
        $sql .= " ORDER BY l.application_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        if (!empty($filters['status'])) {
            $stmt->bindValue(':status', $filters['status']);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAllLoans(array $filters = []) {
        $sql = "SELECT COUNT(*) FROM {$this->loans_table}";
        if (!empty($filters['status'])) {
            $sql .= " WHERE status = :status";
        }
        $stmt = $this->db->prepare($sql);
        if (!empty($filters['status'])) {
            $stmt->bindValue(':status', $filters['status']);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function findInstallmentsByLoanId($loan_id) {
        $sql = "SELECT * FROM {$this->installments_table} WHERE loan_id = :loan_id ORDER BY installment_number ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':loan_id' => $loan_id]);
        return $stmt->fetchAll();
    }

    /**
     * Membuat aplikasi pinjaman baru oleh anggota.
     */
    public function apply(array $data)
    {
        $sql = "INSERT INTO {$this->loans_table} (member_id, loan_amount, tenor_months, interest_rate_percent, purpose, status)
                VALUES (:member_id, :loan_amount, :tenor_months, :interest_rate_percent, :purpose, 'pending')";

        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([
            ':member_id' => $data['member_id'],
            ':loan_amount' => $data['loan_amount'],
            ':tenor_months' => $data['tenor_months'],
            ':interest_rate_percent' => $data['interest_rate_percent'],
            ':purpose' => $data['purpose']
        ])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Menyetujui pinjaman dan membuat jadwal angsuran (transaksional).
     */
    public function approve($loan_id, $admin_id)
    {
        $this->db->beginTransaction();
        try {
            // 1. Update status pinjaman
            $loanSql = "UPDATE {$this->loans_table}
                        SET status = 'approved', approved_by_admin_id = :admin_id, approval_date = NOW()
                        WHERE id = :loan_id AND status = 'pending'";
            $loanStmt = $this->db->prepare($loanSql);
            $loanStmt->execute([':admin_id' => $admin_id, ':loan_id' => $loan_id]);

            // 2. Generate jadwal angsuran
            $this->_generateAmortizationSchedule($loan_id);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            // Log error $e->getMessage()
            return false;
        }
    }

    /**
     * Menolak aplikasi pinjaman.
     */
    public function reject($loan_id, $admin_id)
    {
        $sql = "UPDATE {$this->loans_table} SET status = 'rejected', approved_by_admin_id = :admin_id WHERE id = :loan_id AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':admin_id' => $admin_id, ':loan_id' => $loan_id]);
    }

    /**
     * Mencatat pembayaran angsuran (transaksional), mendukung pembayaran parsial.
     */
    public function recordPayment(array $data)
    {
        $this->db->beginTransaction();
        try {
            // 1. Dapatkan dan kunci angsuran
            $instSql = "SELECT * FROM {$this->installments_table} WHERE id = :id FOR UPDATE";
            $instStmt = $this->db->prepare($instSql);
            $instStmt->execute([':id' => $data['installment_id']]);
            $installment = $instStmt->fetch();

            if (!$installment || $installment['status'] === 'paid') {
                throw new Exception("Angsuran tidak valid atau sudah lunas.");
            }

            // 2. Logika pembayaran parsial
            $totalDue = (float)$installment['amount_due'] + (float)$installment['late_fee'];
            $newAmountPaid = (float)$installment['amount_paid'] + (float)$data['payment_amount'];
            $newStatus = $installment['status'];

            if ($newAmountPaid >= $totalDue) {
                $newStatus = 'paid';
            }

            // 3. Update angsuran
            $updateSql = "UPDATE {$this->installments_table} SET status = :status, payment_date = NOW(), amount_paid = :amount_paid WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                ':status' => $newStatus,
                ':amount_paid' => $newAmountPaid,
                ':id' => $data['installment_id']
            ]);

            // 4. Cek apakah semua angsuran sudah lunas
            $checkSql = "SELECT COUNT(*) as pending_count FROM {$this->installments_table} WHERE loan_id = :loan_id AND status = 'pending'";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([':loan_id' => $installment['loan_id']]);
            $pendingCount = $checkStmt->fetchColumn();

            if ($pendingCount == 0) {
                // 4. Jika lunas, update status pinjaman utama
                $loanSql = "UPDATE {$this->loans_table} SET status = 'completed' WHERE id = :loan_id";
                $loanStmt = $this->db->prepare($loanSql);
                $loanStmt->execute([':loan_id' => $installment['loan_id']]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Log error $e->getMessage()
            return false;
        }
    }

    /**
     * Menghitung dan menyimpan jadwal angsuran (bunga menurun).
     */
    private function _generateAmortizationSchedule($loan_id)
    {
        $loanStmt = $this->db->prepare("SELECT * FROM {$this->loans_table} WHERE id = :id");
        $loanStmt->execute([':id' => $loan_id]);
        $loan = $loanStmt->fetch();

        $principal = (float)$loan['loan_amount'];
        $rate = (float)$loan['interest_rate_percent'] / 100 / 12; // Bunga bulanan
        $tenor = (int)$loan['tenor_months'];

        // Formula angsuran bulanan (PMT)
        $monthlyPayment = $principal * ($rate * pow(1 + $rate, $tenor)) / (pow(1 + $rate, $tenor) - 1);
        $remainingBalance = $principal;

        $insertSql = "INSERT INTO {$this->installments_table}
                        (loan_id, installment_number, due_date, amount_due, principal_amount, interest_amount)
                      VALUES (:loan_id, :installment_number, :due_date, :amount_due, :principal_amount, :interest_amount)";
        $insertStmt = $this->db->prepare($insertSql);

        for ($i = 1; $i <= $tenor; $i++) {
            $interest = $remainingBalance * $rate;
            $principalPortion = $monthlyPayment - $interest;
            $remainingBalance -= $principalPortion;

            // Handle last payment to avoid rounding errors
            if ($i == $tenor) {
                $principalPortion += $remainingBalance;
                $monthlyPayment = $principalPortion + $interest;
            }

            $dueDate = new DateTime($loan['approval_date']);
            $dueDate->add(new DateInterval("P{$i}M"));

            $insertStmt->execute([
                ':loan_id' => $loan_id,
                ':installment_number' => $i,
                ':due_date' => $dueDate->format('Y-m-d'),
                ':amount_due' => round($monthlyPayment, 2),
                ':principal_amount' => round($principalPortion, 2),
                ':interest_amount' => round($interest, 2)
            ]);
        }
    }

    /**
     * Menemukan semua angsuran yang jatuh tempo dan menerapkan denda.
     */
    public function applyLateFees()
    {
        $fineAmount = 50000; // Contoh denda tetap
        $sql = "UPDATE {$this->installments_table}
                SET status = 'overdue', late_fee = late_fee + :fine_amount
                WHERE due_date < CURDATE() AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fine_amount' => $fineAmount]);

        return $stmt->rowCount();
    }
}

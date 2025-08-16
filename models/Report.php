<?php

require_once 'Model.php';

class Report extends Model
{
    /**
     * Mengambil data untuk laporan arus kas dalam rentang tanggal tertentu.
     * Menggabungkan beberapa sumber transaksi menjadi satu timeline.
     */
    public function getCashFlowData($startDate, $endDate)
    {
        $sql = "
            SELECT
                transaction_date AS date,
                SUM(CASE WHEN transaction_type = 'cash_in' THEN amount ELSE 0 END) AS total_in,
                SUM(CASE WHEN transaction_type = 'cash_out' THEN amount ELSE 0 END) AS total_out
            FROM (
                -- Uang Masuk dari Simpanan
                SELECT transaction_date, amount, 'cash_in' as transaction_type
                FROM savings_transactions
                WHERE transaction_type = 'deposit' AND transaction_date BETWEEN :start_date1 AND :end_date1

                UNION ALL

                -- Uang Keluar untuk Penarikan
                SELECT transaction_date, amount, 'cash_out' as transaction_type
                FROM savings_transactions
                WHERE transaction_type = 'withdrawal' AND transaction_date BETWEEN :start_date2 AND :end_date2

                UNION ALL

                -- Uang Masuk dari Pembayaran Pinjaman
                SELECT payment_date as transaction_date, payment_amount as amount, 'cash_in' as transaction_type
                FROM loan_payments
                WHERE payment_date BETWEEN :start_date3 AND :end_date3

                UNION ALL

                -- Uang Keluar untuk Pencairan Pinjaman
                SELECT approval_date as transaction_date, loan_amount as amount, 'cash_out' as transaction_type
                FROM loans
                WHERE status = 'approved' AND approval_date IS NOT NULL AND approval_date BETWEEN :start_date4 AND :end_date4
            ) AS cash_flow
            GROUP BY DATE(transaction_date)
            ORDER BY date ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date1' => $startDate, ':end_date1' => $endDate,
            ':start_date2' => $startDate, ':end_date2' => $endDate,
            ':start_date3' => $startDate, ':end_date3' => $endDate,
            ':start_date4' => $startDate, ':end_date4' => $endDate,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Mengambil data untuk laporan laba rugi.
     * Saat ini hanya menghitung pendapatan bunga dari pinjaman.
     */
    public function getProfitLossData($startDate, $endDate)
    {
        $sql = "SELECT
                    DATE(payment_date) as date,
                    SUM(interest_amount) as total_interest_income
                FROM loan_installments
                WHERE status = 'paid' AND payment_date BETWEEN :start_date AND :end_date
                GROUP BY DATE(payment_date)
                ORDER BY date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Mengambil data untuk laporan neraca.
     */
    public function getBalanceSheetData()
    {
        // 1. Hitung Total Aset Kas
        $cashSql = "SELECT SUM(total_in) - SUM(total_out) as total_cash FROM (
                        SELECT SUM(CASE WHEN transaction_type = 'cash_in' THEN amount ELSE 0 END) AS total_in,
                               SUM(CASE WHEN transaction_type = 'cash_out' THEN amount ELSE 0 END) AS total_out
                        FROM (
                            SELECT amount, 'cash_in' as transaction_type FROM savings_transactions WHERE transaction_type = 'deposit'
                            UNION ALL
                            SELECT amount, 'cash_out' as transaction_type FROM savings_transactions WHERE transaction_type = 'withdrawal'
                            UNION ALL
                            SELECT payment_amount as amount, 'cash_in' as transaction_type FROM loan_payments
                            UNION ALL
                            SELECT loan_amount as amount, 'cash_out' as transaction_type FROM loans WHERE status = 'approved'
                        ) as all_transactions
                    ) as cash_summary";
        $cash = $this->db->query($cashSql)->fetchColumn();

        // 2. Hitung Total Piutang Pinjaman (Outstanding Principal)
        $receivablesSql = "SELECT
                                SUM(total_principal) - SUM(paid_principal) as total_receivables
                           FROM (
                                SELECT
                                    li.loan_id,
                                    SUM(li.principal_amount) as total_principal,
                                    SUM(CASE WHEN li.status = 'paid' THEN li.principal_amount ELSE 0 END) as paid_principal
                                FROM loan_installments li
                                JOIN loans l ON li.loan_id = l.id
                                WHERE l.status = 'approved'
                                GROUP BY li.loan_id
                           ) as loan_summary";
        $receivables = $this->db->query($receivablesSql)->fetchColumn();

        // 3. Hitung Total Kewajiban (Simpanan Anggota)
        $liabilitiesSql = "SELECT SUM(balance) FROM savings_accounts";
        $liabilities = $this->db->query($liabilitiesSql)->fetchColumn();

        return [
            'assets' => [
                'cash' => (float)$cash,
                'receivables' => (float)$receivables,
                'total' => (float)$cash + (float)$receivables
            ],
            'liabilities' => [
                'member_savings' => (float)$liabilities,
                'total' => (float)$liabilities
            ],
            'equity' => ((float)$cash + (float)$receivables) - (float)$liabilities
        ];
    }

    /**
     * Mengambil data untuk laporan aktivitas anggota (pendaftaran baru).
     */
    public function getMemberActivityData($startDate, $endDate)
    {
        $sql = "SELECT
                    DATE(join_date) as date,
                    COUNT(*) as new_members
                FROM members
                WHERE join_date BETWEEN :start_date AND :end_date
                GROUP BY DATE(join_date)
                ORDER BY date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Mengambil data untuk laporan aktivitas pinjaman (berdasarkan status).
     */
    public function getLoanActivityData()
    {
        $sql = "SELECT status, COUNT(*) as count FROM loans GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

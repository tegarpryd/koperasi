<?php

require_once '../models/Savings.php';
require_once '../models/Member.php';

class SavingsController extends Controller
{
    /**
     * Menampilkan detail simpanan untuk seorang anggota.
     */
    public function show(Request $request, $member_id)
    {
        $this->authorize(['admin']);

        $savingsModel = new Savings();
        $memberModel = new Member();

        $member = $memberModel->findById($member_id);
        if (!$member) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Anggota Tidak Ditemukan']);
            return;
        }

        $page = (int)($request->getQuery('page', 1));
        $limit = 5; // Smaller limit for transaction history
        $offset = ($page - 1) * $limit;

        $accounts = $savingsModel->findAccountsByMemberId($member_id);
        $transactions = $savingsModel->findTransactionsByMemberId($member_id, $limit, $offset);
        $totalTransactions = $savingsModel->countTransactionsByMemberId($member_id);
        $totalPages = ceil($totalTransactions / $limit);
        $csrfToken = Security::generateToken();

        $this->render('admin/savings/show', [
            'title' => 'Detail Simpanan: ' . $member['full_name'],
            'member' => $member,
            'accounts' => $accounts,
            'transactions' => $transactions,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * Menyimpan transaksi simpanan baru (setoran atau penarikan).
     */
    public function storeTransaction(Request $request)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();
        $member_id = $data['member_id'] ?? null;

        // Validasi CSRF Token
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Sesi tidak valid. Silakan coba lagi.'];
            $this->redirect('/admin/members/' . $member_id . '/savings');
            return;
        }

        // Validasi Sederhana
        if (empty($member_id) || !isset($data['amount']) || $data['amount'] <= 0) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Data transaksi tidak valid.'];
            if ($member_id) {
                $this->redirect('/admin/members/' . $member_id . '/savings');
            } else {
                $this->redirect('/admin/members');
            }
            return;
        }

        $savingsModel = new Savings();
        try {
            if ($savingsModel->createTransaction($data)) {
                AuditLogger::log('SAVINGS_TRANSACTION_SUCCESS', "Admin ID: {$_SESSION['user']['id']} created transaction. Data: " . json_encode($data));
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Transaksi berhasil disimpan.'];
            } else {
                AuditLogger::log('SAVINGS_TRANSACTION_FAILURE', "Admin ID: {$_SESSION['user']['id']} failed to create transaction. Data: " . json_encode($data));
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal menyimpan transaksi. Saldo mungkin tidak mencukupi atau terjadi kesalahan sistem.'];
            }
        } catch (PDOException $e) {
             $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error: ' . $e->getMessage()];
        }

        $this->redirect('/admin/members/' . $member_id . '/savings');
    }
}

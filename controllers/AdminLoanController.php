<?php

require_once '../models/Loan.php';
require_once '../models/Notification.php';

class AdminLoanController extends Controller
{
    private $loanModel;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->loanModel = new Loan();
        $this->notificationModel = new Notification();
    }

    /**
     * Menampilkan daftar semua pinjaman (bisa difilter).
     */
    public function index(Request $request)
    {
        $this->authorize(['admin']);
        $filters = ['status' => $request->getQuery('status')];
        $page = (int)($request->getQuery('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $loans = $this->loanModel->findAllLoans(array_filter($filters), $limit, $offset);
        $totalLoans = $this->loanModel->countAllLoans(array_filter($filters));
        $totalPages = ceil($totalLoans / $limit);

        $this->render('admin/loans/index', [
            'title' => 'Manajemen Pinjaman',
            'loans' => $loans,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters
        ]);
    }

    /**
     * Menampilkan detail pinjaman untuk admin.
     */
    public function show($id)
    {
        $this->authorize(['admin']);
        $loan = $this->loanModel->findLoanDetails($id);
        if (!$loan) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Pinjaman Tidak Ditemukan']);
            return;
        }
        $installments = $this->loanModel->findInstallmentsByLoanId($id);
        $this->render('admin/loans/show', ['title' => 'Detail Pinjaman', 'loan' => $loan, 'installments' => $installments]);
    }

    /**
     * Menyetujui pinjaman.
     */
    public function approve($id)
    {
        $this->authorize(['admin']);
        $loan = $this->loanModel->findLoanDetails($id);
        if ($loan && $this->loanModel->approve($id, $_SESSION['user']['id'])) {
            AuditLogger::log('LOAN_APPROVE', "Admin ID: {$_SESSION['user']['id']} approved loan ID: {$id}.");

            // Kirim notifikasi ke anggota
            $loanDetails = $this->loanModel->findLoanDetails($id);
            $memberUserId = $loanDetails['user_id'];
            $message = "Selamat! Pinjaman Anda sebesar Rp " . number_format($loanDetails['loan_amount'], 0, ',', '.') . " telah disetujui.";
            $this->notificationModel->create($memberUserId, $message, "/dashboard/loans/{$id}");

            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pinjaman berhasil disetujui dan jadwal angsuran telah dibuat.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal menyetujui pinjaman.'];
        }
        $this->redirect('/admin/loans/' . $id);
    }

    /**
     * Menolak pinjaman.
     */
    public function reject($id)
    {
        $this->authorize(['admin']);
        $loan = $this->loanModel->findLoanDetails($id);
        if ($loan && $this->loanModel->reject($id, $_SESSION['user']['id'])) {
            AuditLogger::log('LOAN_REJECT', "Admin ID: {$_SESSION['user']['id']} rejected loan ID: {$id}.");

            // Kirim notifikasi ke anggota
            $message = "Mohon maaf, pengajuan pinjaman Anda sebesar Rp " . number_format($loan['loan_amount'], 0, ',', '.') . " tidak dapat disetujui saat ini.";
            $this->notificationModel->create($loan['user_id'], $message, "/dashboard/loans/{$id}");

            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pinjaman telah ditolak.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal menolak pinjaman.'];
        }
        $this->redirect('/admin/loans/' . $id);
    }

    /**
     * Menyimpan pembayaran angsuran.
     */
    public function storePayment(Request $request)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();
        $loan_id = $data['loan_id'] ?? null;

        if ($this->loanModel->recordPayment($data)) {
             AuditLogger::log('LOAN_PAYMENT_SUCCESS', "Admin ID: {$_SESSION['user']['id']} recorded payment. Data: " . json_encode($data));
             $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pembayaran berhasil dicatat.'];
        } else {
             AuditLogger::log('LOAN_PAYMENT_FAILURE', "Admin ID: {$_SESSION['user']['id']} failed to record payment. Data: " . json_encode($data));
             $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal mencatat pembayaran.'];
        }

        if ($loan_id) {
            $this->redirect('/admin/loans/' . $loan_id);
        } else {
            $this->redirect('/admin/loans');
        }
    }

    /**
     * Memeriksa dan menerapkan denda untuk semua angsuran yang jatuh tempo.
     */
    public function checkOverdue()
    {
        $this->authorize(['admin']);
        $updatedCount = $this->loanModel->applyLateFees();
        $_SESSION['flash_message'] = ['type' => 'info', 'message' => "Pemeriksaan selesai. {$updatedCount} angsuran yang jatuh tempo telah diperbarui."];
        $this->redirect('/admin/loans');
    }
}

<?php

require_once '../models/Loan.php';
require_once '../models/Member.php';
require_once '../models/User.php';
require_once '../models/Notification.php';

class LoanController extends Controller
{
    private $loanModel;
    private $memberModel;
    private $userModel;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->loanModel = new Loan();
        $this->memberModel = new Member();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    /**
     * Menampilkan dashboard pinjaman untuk anggota yang sedang login.
     */
    public function index(Request $request)
    {
        $this->authorize(['member']);
        $member = $this->memberModel->findByUserId($_SESSION['user']['id']);

        $page = (int)($request->getQuery('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $loans = $this->loanModel->findLoansByMemberId($member['id'], $limit, $offset);
        $totalLoans = $this->loanModel->countLoansByMemberId($member['id']);
        $totalPages = ceil($totalLoans / $limit);

        $this->render('dashboard/loans/index', [
            'title' => 'Riwayat Pinjaman Saya',
            'loans' => $loans,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Menampilkan form pengajuan pinjaman baru.
     */
    public function apply()
    {
        $this->authorize(['member']);
        $csrfToken = Security::generateToken();
        $this->render('dashboard/loans/apply', ['title' => 'Ajukan Pinjaman Baru', 'csrf_token' => $csrfToken]);
    }

    /**
     * Menyimpan pengajuan pinjaman baru.
     */
    public function store(Request $request)
    {
        $this->authorize(['member']);
        $data = $request->getBody();

        // Validasi CSRF Token
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Sesi tidak valid. Silakan coba lagi.'];
            $this->redirect('/dashboard/loans/apply');
            return;
        }

        $member = $this->memberModel->findByUserId($_SESSION['user']['id']);
        $data['member_id'] = $member['id'];

        // TODO: Add complex validation

        $loanId = $this->loanModel->apply($data);
        if ($loanId) {
             $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Aplikasi pinjaman berhasil diajukan.'];

             // Kirim notifikasi ke semua admin
             $admins = $this->userModel->findAllAdmins();
             $message = "Pengajuan pinjaman baru dari {$member['full_name']} sebesar Rp " . number_format($data['loan_amount'], 0, ',', '.');
             foreach ($admins as $adminId) {
                 $this->notificationModel->create($adminId, $message, "/admin/loans/{$loanId}");
             }
        } else {
             $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal mengajukan aplikasi pinjaman.'];
        }
        $this->redirect('/dashboard/loans');
    }

    /**
     * Menampilkan detail satu pinjaman milik anggota.
     */
    public function show($id)
    {
        $this->authorize(['member']);
        $member = $this->memberModel->findByUserId($_SESSION['user']['id']);
        $loan = $this->loanModel->findLoanDetails($id);

        // Pastikan anggota hanya bisa melihat pinjamannya sendiri
        if (!$loan || $loan['member_id'] !== $member['id']) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Pinjaman Tidak Ditemukan']);
            return;
        }

        $installments = $this->loanModel->findInstallmentsByLoanId($id);

        $this->render('dashboard/loans/show', [
            'title' => 'Detail Pinjaman',
            'loan' => $loan,
            'installments' => $installments
        ]);
    }
}

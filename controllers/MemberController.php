<?php

require_once '../models/Member.php';

class MemberController extends Controller
{
    /**
     * Menampilkan daftar semua anggota.
     */
    public function index(Request $request)
    {
        $this->authorize(['admin']);
        $memberModel = new Member();

        $page = (int)($request->getQuery('page', 1));
        $limit = 10; // Records per page
        $offset = ($page - 1) * $limit;

        $members = $memberModel->findAll($limit, $offset);
        $totalMembers = $memberModel->countAll();
        $totalPages = ceil($totalMembers / $limit);

        $this->render('admin/members/index', [
            'title' => 'Manajemen Anggota',
            'members' => $members,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Menampilkan form untuk membuat anggota baru.
     */
    public function create()
    {
        $this->authorize(['admin']);
        $this->render('admin/members/create', ['title' => 'Tambah Anggota Baru']);
    }

    /**
     * Menyimpan anggota baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();

        // Validasi input yang lebih kompleks
        $errors = [];
        if (empty($data['full_name'])) {
            $errors[] = 'Nama lengkap tidak boleh kosong.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'Password harus memiliki minimal 8 karakter.';
        }
        if (!empty($errors)) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => implode('<br>', $errors)];
            $this->redirect('/admin/members/create');
            return;
        }

        $memberModel = new Member();
        $newMemberId = $memberModel->create($data);
        if ($newMemberId) {
            AuditLogger::log('MEMBER_CREATE', "Admin ID: {$_SESSION['user']['id']} created new member. Data: " . json_encode($data));
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Anggota baru berhasil ditambahkan.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal menambahkan anggota baru.'];
        }
        $this->redirect('/admin/members');
    }

    /**
     * Menampilkan form untuk mengedit anggota.
     */
    public function edit($id)
    {
        $this->authorize(['admin']);
        $memberModel = new Member();
        $member = $memberModel->findById($id);

        if (!$member) {
            // Tampilkan error 404 jika anggota tidak ditemukan
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Tidak Ditemukan']);
            exit();
        }

        $this->render('admin/members/edit', ['title' => 'Edit Anggota', 'member' => $member]);
    }

    /**
     * Mengupdate data anggota di database.
     */
    public function update(Request $request, $id)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();

        // TODO: Tambahkan validasi data yang lebih kompleks di sini

        $memberModel = new Member();
        if ($memberModel->update($id, $data)) {
            AuditLogger::log('MEMBER_UPDATE', "Admin ID: {$_SESSION['user']['id']} updated member ID: {$id}. Data: " . json_encode($data));
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Data anggota berhasil diperbarui.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal memperbarui data anggota.'];
        }
        $this->redirect('/admin/members');
    }

    /**
     * Menghapus anggota dari database.
     */
    public function delete($id)
    {
        $this->authorize(['admin']);
        $memberModel = new Member();
        if ($memberModel->delete($id)) {
            AuditLogger::log('MEMBER_DELETE', "Admin ID: {$_SESSION['user']['id']} deleted member ID: {$id}.");
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Anggota berhasil dihapus.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal menghapus anggota.'];
        }
        $this->redirect('/admin/members');
    }
}

<?php

require_once '../models/Setting.php';
require_once '../models/StaticPage.php';

class SettingsController extends Controller
{
    private $settingModel;
    private $pageModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = new Setting();
        $this->pageModel = new StaticPage();
    }

    // General Settings
    public function index()
    {
        $this->authorize(['admin']);
        $settings = $this->settingModel->getAllAsAssoc();
        $csrfToken = Security::generateToken();
        $this->render('admin/settings/index', [
            'title' => 'Pengaturan Situs',
            'settings' => $settings,
            'csrf_token' => $csrfToken
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Sesi tidak valid.'];
            $this->redirect('/admin/settings');
            return;
        }

        if ($this->settingModel->updateAll($data['settings'])) {
            AuditLogger::log('SETTINGS_UPDATE', "Admin ID: {$_SESSION['user']['id']} updated site settings.");
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pengaturan berhasil diperbarui.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal memperbarui pengaturan.'];
        }
        $this->redirect('/admin/settings');
    }

    // Static Pages CRUD
    public function listPages()
    {
        $this->authorize(['admin']);
        $pages = $this->pageModel->findAll();
        $csrfToken = Security::generateToken();
        $this->render('admin/pages/index', [
            'title' => 'Manajemen Halaman Statis',
            'pages' => $pages,
            'csrf_token' => $csrfToken
        ]);
    }

    public function createPage()
    {
        $this->authorize(['admin']);
        $csrfToken = Security::generateToken();
        $this->render('admin/pages/create', ['title' => 'Buat Halaman Baru', 'csrf_token' => $csrfToken]);
    }

    public function storePage(Request $request)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
             $this->redirect('/admin/pages'); return;
        }

        if ($this->pageModel->create($data)) {
            AuditLogger::log('PAGE_CREATE', "Admin ID: {$_SESSION['user']['id']} created page '{$data['title']}'.");
             $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Halaman berhasil dibuat.'];
        } else {
             $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal membuat halaman.'];
        }
        $this->redirect('/admin/pages');
    }

    public function editPage($id)
    {
        $this->authorize(['admin']);
        $page = $this->pageModel->findById($id);
        $csrfToken = Security::generateToken();
        $this->render('admin/pages/edit', ['title' => 'Edit Halaman', 'page' => $page, 'csrf_token' => $csrfToken]);
    }

    public function updatePage(Request $request, $id)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
             $this->redirect('/admin/pages'); return;
        }

        if ($this->pageModel->update($id, $data)) {
            AuditLogger::log('PAGE_UPDATE', "Admin ID: {$_SESSION['user']['id']} updated page ID: {$id}.");
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Halaman berhasil diperbarui.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal memperbarui halaman.'];
        }
        $this->redirect('/admin/pages');
    }

    public function deletePage(Request $request, $id)
    {
        $this->authorize(['admin']);
        $data = $request->getBody();
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
             $this->redirect('/admin/pages'); return;
        }

        if ($this->pageModel->delete($id)) {
            AuditLogger::log('PAGE_DELETE', "Admin ID: {$_SESSION['user']['id']} deleted page ID: {$id}.");
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Halaman berhasil dihapus.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Gagal menghapus halaman.'];
        }
        $this->redirect('/admin/pages');
    }
}

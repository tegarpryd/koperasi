<?php

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     * Hanya bisa diakses oleh admin.
     */
    public function index()
    {
        // Terapkan proteksi RBAC, hanya izinkan 'admin'
        $this->authorize(['admin']);

        // Jika lolos, render view dashboard admin
        $this->render('admin/dashboard', ['title' => 'Admin Dashboard']);
    }
}

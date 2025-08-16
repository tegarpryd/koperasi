<?php

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama (homepage).
     */
    public function index()
    {
        $this->render('home/index', ['title' => 'Halaman Utama']);
    }
}

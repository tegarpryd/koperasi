<?php

require_once '../models/StaticPage.php';

class PageController extends Controller
{
    private $pageModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new StaticPage();
    }

    /**
     * Menampilkan halaman statis berdasarkan slug.
     */
    public function show(Request $request, $slug)
    {
        $page = $this->pageModel->findBySlug($slug);

        if ($page && $page['is_published']) {
            $this->render('page/show', [
                'title' => $page['title'],
                'page' => $page
            ]);
        } else {
            // Jika tidak ada halaman, teruskan ke resolver router untuk 404
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Halaman Tidak Ditemukan']);
        }
    }
}

<?php

require_once '../models/Report.php';

class ReportController extends Controller
{
    private $reportModel;

    public function __construct()
    {
        $this->reportModel = new Report();
    }

    /**
     * Menampilkan dashboard laporan utama.
     */
    public function index()
    {
        $this->authorize(['admin']);
        $this->render('admin/reports/index', ['title' => 'Laporan & Analitik']);
    }

    /**
     * Menampilkan laporan arus kas.
     */
    public function cashFlow()
    {
        $this->authorize(['admin']);

        // Tentukan rentang tanggal, default ke bulan ini
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $cashFlowData = $this->reportModel->getCashFlowData($startDate, $endDate);

        $this->render('admin/reports/cash_flow', [
            'title' => 'Laporan Arus Kas',
            'cashFlowData' => $cashFlowData
        ]);
    }

    /**
     * Menampilkan laporan laba rugi.
     */
    public function profitloss()
    {
        $this->authorize(['admin']);

        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $profitLossData = $this->reportModel->getProfitLossData($startDate, $endDate);

        $this->render('admin/reports/profit_loss', [
            'title' => 'Laporan Laba Rugi',
            'profitLossData' => $profitLossData
        ]);
    }

    /**
     * Menampilkan laporan neraca.
     */
    public function balanceSheet()
    {
        $this->authorize(['admin']);
        $balanceSheetData = $this->reportModel->getBalanceSheetData();
        $this->render('admin/reports/balance_sheet', [
            'title' => 'Laporan Neraca',
            'balanceSheetData' => $balanceSheetData
        ]);
    }

    /**
     * Menampilkan laporan aktivitas anggota.
     */
    public function memberActivity()
    {
        $this->authorize(['admin']);
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $memberActivityData = $this->reportModel->getMemberActivityData($startDate, $endDate);

        $this->render('admin/reports/member_activity', [
            'title' => 'Laporan Aktivitas Anggota',
            'memberActivityData' => $memberActivityData
        ]);
    }

    /**
     * Menampilkan laporan aktivitas pinjaman.
     */
    public function loanActivity()
    {
        $this->authorize(['admin']);
        $loanActivityData = $this->reportModel->getLoanActivityData();
        $this->render('admin/reports/loan_activity', [
            'title' => 'Laporan Aktivitas Pinjaman',
            'loanActivityData' => $loanActivityData
        ]);
    }
}

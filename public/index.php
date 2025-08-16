<?php

// Mulai session
session_start();

// Muat file-file inti
require_once '../core/Router.php';
require_once '../core/Request.php';
require_once '../core/Controller.php';
require_once '../core/Security.php';
require_once '../core/AuditLogger.php';

// Muat file konfigurasi
require_once '../config/database.php';

// Muat semua model
foreach (glob("../models/*.php") as $filename) {
    require_once $filename;
}

// Muat semua controller
foreach (glob("../controllers/*.php") as $filename) {
    require_once $filename;
}

// Inisialisasi Router
$router = new Router(new Request);

// Definisikan rute (routes) di sini
$router->get('/', 'HomeController@index');

// Rute Autentikasi
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegistrationForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

$router->adminGroup(function (Router $router) {
    // Dashboard
    $router->get('/', 'AdminController@index');

    // Member CRUD
    $router->get('/members', 'MemberController@index');
    $router->get('/members/create', 'MemberController@create');
    $router->post('/members', 'MemberController@store');
    $router->get('/members/edit/{id}', 'MemberController@edit');
    $router->post('/members/update/{id}', 'MemberController@update');
    $router->post('/members/delete/{id}', 'MemberController@delete');

    // Savings
    $router->get('/members/{id}/savings', 'SavingsController@show');
    $router->post('/savings/transaction', 'SavingsController@storeTransaction');

    // Loan Management
    $router->get('/loans', 'AdminLoanController@index');
    $router->get('/loans/{id}', 'AdminLoanController@show');
    $router->post('/loans/{id}/approve', 'AdminLoanController@approve');
    $router->post('/loans/{id}/reject', 'AdminLoanController@reject');
    $router->post('/loans/payment', 'AdminLoanController@storePayment');
    $router->post('/loans/check-overdue', 'AdminLoanController@checkOverdue');

    // Reports
    $router->get('/reports', 'ReportController@index');
    $router->get('/reports/cash-flow', 'ReportController@cashFlow');
    $router->get('/reports/profit-loss', 'ReportController@profitloss');
    $router->get('/reports/balance-sheet', 'ReportController@balanceSheet');
    $router->get('/reports/member-activity', 'ReportController@memberActivity');
    $router->get('/reports/loan-activity', 'ReportController@loanActivity');

    // Settings & CMS
    $router->get('/settings', 'SettingsController@index');
    $router->post('/settings', 'SettingsController@update');
    $router->get('/pages', 'SettingsController@listPages');
    $router->get('/pages/create', 'SettingsController@createPage');
    $router->post('/pages', 'SettingsController@storePage');
    $router->get('/pages/{id}/edit', 'SettingsController@editPage');
    $router->post('/pages/{id}', 'SettingsController@updatePage');
    $router->post('/pages/{id}/delete', 'SettingsController@deletePage');
});

// Rute Pinjaman (Sisi Anggota)
$router->get('/dashboard/loans', 'LoanController@index');
$router->get('/dashboard/loans/apply', 'LoanController@apply');
$router->post('/dashboard/loans', 'LoanController@store');
$router->get('/dashboard/loans/{id}', 'LoanController@show');

// Rute Notifikasi
$router->get('/notifications', 'NotificationController@index');
$router->post('/notifications/{id}/read', 'NotificationController@markAsRead');

// Rute Halaman Statis (Catch-all) - HARUS DIDEFINISIKAN TERAKHIR
$router->get('/{slug}', 'PageController@show');


// Jalankan router untuk menangani permintaan
$router->resolve();

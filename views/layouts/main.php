<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Aplikasi Simpan Pinjam' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/dynamic-theme.php">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">
        <img src="<?= htmlspecialchars($settings['logo_path'] ?? '/assets/img/logo.png') ?>" alt="Logo" style="height: 30px; margin-right: 10px;">
        <?= htmlspecialchars($settings['company_name'] ?? 'Koperasi Maju Jaya') ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Notifikasi
                    <?php if (isset($notificationData['unread_count']) && $notificationData['unread_count'] > 0): ?>
                        <span class="badge bg-danger"><?= $notificationData['unread_count'] ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if (!empty($notificationData['unread_list'])): ?>
                        <?php foreach ($notificationData['unread_list'] as $notif): ?>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($notif['link']) ?>"><?= htmlspecialchars($notif['message']) ?></a></li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item text-center" href="/notifications">Lihat Semua</a></li>
                </ul>
            </li>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin Panel
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/<?= htmlspecialchars($admin_path) ?>">Dashboard Admin</a></li>
                        <li><a class="dropdown-item" href="/<?= htmlspecialchars($admin_path) ?>/members">Manajemen Anggota</a></li>
                        <li><a class="dropdown-item" href="/<?= htmlspecialchars($admin_path) ?>/loans">Manajemen Pinjaman</a></li>
                        <li><a class="dropdown-item" href="/<?= htmlspecialchars($admin_path) ?>/reports">Laporan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/<?= htmlspecialchars($admin_path) ?>/settings">Pengaturan Situs</a></li>
                        <li><a class="dropdown-item" href="/<?= htmlspecialchars($admin_path) ?>/pages">Manajemen Halaman</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item">
                  <a class="nav-link" href="/dashboard">Dashboard</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link" href="/logout">Logout</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="/login">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/register">Register</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container mt-4">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?>" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <?= $content ?? '' ?>
</main>

<footer class="text-center mt-5 py-3 bg-light">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['company_name'] ?? 'Koperasi Maju Jaya') ?>. All Rights Reserved.</p>
    <p><?= htmlspecialchars($settings['company_address'] ?? '') ?> | <?= htmlspecialchars($settings['company_contact'] ?? '') ?></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/script.js"></script>
</body>
</html>

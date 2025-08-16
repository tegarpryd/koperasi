<div class="container mt-4">
    <h2>Semua Notifikasi</h2>

    <div class="list-group">
        <?php if (isset($notifications) && !empty($notifications)): ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item list-group-item-action <?= $notif['is_read'] ? '' : 'list-group-item-info' ?>">
                    <a href="<?= htmlspecialchars($notif['link']) ?>" class="text-decoration-none text-dark">
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1"><?= htmlspecialchars($notif['message']) ?></p>
                            <small><?= htmlspecialchars(date('d M Y H:i', strtotime($notif['created_at']))) ?></small>
                        </div>
                    </a>
                    <?php if (!$notif['is_read']): ?>
                        <form action="/notifications/<?= $notif['id'] ?>/read" method="POST" class="mt-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Tandai sudah dibaca</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Anda tidak memiliki notifikasi.</p>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <?php require_once '../views/partials/pagination.php'; ?>
    </div>
</div>

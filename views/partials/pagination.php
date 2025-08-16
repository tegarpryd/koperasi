<?php if ($totalPages > 1): ?>
<?php
// Preserve existing query string parameters
$queryParams = $_GET;
?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <?php $queryParams['page'] = $currentPage - 1; ?>
            <a class="page-link" href="?<?= http_build_query($queryParams) ?>" tabindex="-1" aria-disabled="true">Previous</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $queryParams['page'] = $i; ?>
            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query($queryParams) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Next Button -->
        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
            <?php $queryParams['page'] = $currentPage + 1; ?>
            <a class="page-link" href="?<?= http_build_query($queryParams) ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

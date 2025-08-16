<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h1><?= htmlspecialchars($page['title']) ?></h1>
        </div>
        <div class="card-body">
            <?php
                // WARNING: For a production environment, a proper HTML Purifier library is recommended
                // to prevent sophisticated XSS attacks. strip_tags is a basic first line of defense.
                $allowed_tags = '<p><a><strong><em><ul><ol><li><br><h1><h2><h3><h4><h5><h6><img><blockquote>';
                echo strip_tags($page['body'], $allowed_tags);
            ?>
        </div>
    </div>
</div>

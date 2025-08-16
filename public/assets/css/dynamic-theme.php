<?php
// Set the content type to CSS
header('Content-type: text/css');

// This is a simplified bootstrap process just for this file.
// It's not ideal, but necessary to get DB settings without loading the whole app.
require_once '../../../config/database.php';
require_once '../../../models/Setting.php';

$settings = [];
try {
    $settingModel = new Setting();
    $settings = $settingModel->getAllAsAssoc();
} catch (Exception $e) {
    // Fallback to defaults if DB is not ready
}

// Get colors with fallbacks
$primaryColor = htmlspecialchars($settings['theme_primary_color'] ?? '#0d6efd');
$secondaryColor = htmlspecialchars($settings['theme_secondary_color'] ?? '#6c757d');
$textColor = htmlspecialchars($settings['theme_text_color'] ?? '#212529');

?>
:root {
    --primary-color: <?= $primaryColor ?>;
    --secondary-color: <?= $secondaryColor ?>;
    --text-color: <?= $textColor ?>;

    /* You can derive other colors too */
    --bs-primary: var(--primary-color);
    --bs-secondary: var(--secondary-color);
    --bs-body-color: var(--text-color);
}

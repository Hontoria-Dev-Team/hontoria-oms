<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/* AboutUsSidebarComponents.php */

$sectionItems = [
    ['id' => 'history',   'icon' => 'fa-book-open',      'label' => 'Our Story'],
    ['id' => 'location',  'icon' => 'fa-map-marker-alt', 'label' => 'Location'],
    ['id' => 'workplace', 'icon' => 'fa-store',          'label' => 'Inside the Shop'],
    ['id' => 'owner',     'icon' => 'fa-user-tie',       'label' => 'The Owner'],

    /**['id' => 'employees', 'icon' => 'fa-users',          'label' => 'Our Team'],
 */
];

// UPDATED: Changed the direct file paths to use your index.php routing
$pageLinks = [
    ['url' => 'index.php?page=home',     'icon' => 'fa-home',  'label' => 'HOME'],
    ['url' => 'index.php?page=services', 'icon' => 'fa-print', 'label' => 'SERVICES'],
];
?>

<aside class="about-sidebar" id="aboutSidebar">

    <div class="about-sidebar-brand">
        <span class="about-sidebar-title">ABOUT US</span>
    </div>

    <nav class="about-sidebar-pages">
        <?php foreach ($pageLinks as $link): ?>
            <a href="<?= e($link['url']) ?>" class="about-sidebar-page-link">
                <i class="fas <?= e($link['icon']) ?> about-sidebar-icon"></i>
                <span><?= e($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="about-sidebar-divider"></div>

    <nav class="about-sidebar-nav">
        <?php foreach ($sectionItems as $item): ?>
            <a href="#<?= e($item['id']) ?>"
                class="about-sidebar-link"
                data-section="<?= e($item['id']) ?>">
                <i class="fas <?= e($item['icon']) ?> about-sidebar-icon"></i>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

</aside>
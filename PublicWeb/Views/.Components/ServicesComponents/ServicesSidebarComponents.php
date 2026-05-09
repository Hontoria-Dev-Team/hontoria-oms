<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * ServicesSidebarComponents.php
 * Expects $sharedCategories to be passed from ServicesPage.php
 * which gets it from PublicC::showServicesPage()
 */

$logoPath         = $logoPath         ?? '../.Images/Logo.png';
$sharedCategories = $sharedCategories ?? [];
?>

<aside class="sidebar">

    <div class="sidebar-brand">
        <img src="<?= e($logoPath) ?>"
            alt="Logo"
            class="sb-logo"
            onerror="this.style.display='none'" />
        <div class="sb-brand-text">
            <span class="sb-name">HONTORIA</span>
            <span class="sb-name">PRINTING</span>
            <span class="sb-name">SERVICES</span>
        </div>
    </div>

    <div class="sb-search">
        <i class="fas fa-search sb-search-icon"></i>
        <input type="text"
            id="searchInput"
            placeholder="Search Here"
            class="sb-search-input" />
    </div>

    <nav class="sb-nav">

        <a href="?page=home" class="sb-link">
            <i class="fas fa-home sb-icon"></i> HOME
        </a>

        <div class="sb-group">

            <button class="sb-link sb-toggle" id="toggleServices" data-filter="all">
                <i class="fas fa-print sb-icon"></i> SERVICES
                <i class="fas fa-chevron-down sb-chevron" id="chevServices"></i>
            </button>

            <div class="sb-sub" id="subServices">

                <?php foreach ($sharedCategories as $cat): ?>
                    <?php
                    $categoryId = e($cat['id']);
                    $toggleId   = 'toggle_' . $categoryId;
                    $subId      = 'sub_'    . $categoryId;
                    $chevId     = 'chev_'   . $categoryId;
                    ?>

                    <button class="sb-sub-toggle"
                        id="<?= $toggleId ?>"
                        data-filter="<?= $categoryId ?>">
                        <i class="fas <?= e($cat['icon']) ?>"></i>
                        <?= e($cat['label']) ?>
                        <i class="fas fa-chevron-down sb-chevron"
                            id="<?= $chevId ?>"></i>
                    </button>

                    <div class="sb-sub-items" id="<?= $subId ?>">
                        <?php foreach ($cat['items'] as $item): ?>
                            <a href="#"
                                class="sb-item"
                                data-name="<?= e($item) ?>">
                                <?= e($item) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <a href="?page=about" class="sb-link">
            <i class="fas fa-info-circle sb-icon"></i> ABOUT US
        </a>

    </nav>

</aside>
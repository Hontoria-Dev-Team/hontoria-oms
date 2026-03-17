<?php
require_once __DIR__ . '/../reusable_components/Component.php';
/**
 * ServicesSidebarComponent.php
 * Renders sidebar navigation with category filtering.
 *
 * HOW TO ADD A NEW CATEGORY TO THE SIDEBAR:
 * Add a new entry inside renderServicesSub() following the same pattern.
 */
class ServicesSidebarComponent extends \Component {

    public function render(): string {
        $logoPath = $this->get('logoPath', 'img/logo.jpg');

        ob_start();
        ?>
        <aside class="sidebar">
            <?php echo $this->renderBrand($logoPath); ?>
            <?php echo $this->renderSearch(); ?>
            <?php echo $this->renderNav(); ?>
        </aside>
        <?php
        return ob_get_clean();
    }

    // ── Brand / logo at top of sidebar ────────────────────────────────────
    private function renderBrand(string $logoPath): string {
        ob_start();
        ?>
        <div class="sidebar-brand">
            <img src="<?php echo $this->escape($logoPath); ?>" alt="Logo" class="sb-logo"
                 onerror="this.style.display='none'"/>
            <div class="sb-brand-text">
                <span class="sb-name">HONTORIA</span>
                <span class="sb-name">PRINTING</span>
                <span class="sb-name">SERVICES</span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // ── Search box ────────────────────────────────────────────────────────
    private function renderSearch(): string {
        return <<<HTML
        <div class="sb-search">
            <i class="fas fa-search sb-search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search Here" class="sb-search-input"/>
        </div>
        HTML;
    }

    // ── Sidebar navigation ────────────────────────────────────────────────
    private function renderNav(): string {
        ob_start();
        ?>
        <nav class="sb-nav">

            <!-- Home link -->
            <a href="?page=home" class="sb-link">
                <i class="fas fa-home sb-icon"></i> HOME
            </a>

            <!-- Services group with all categories -->
            <div class="sb-group">
                <button class="sb-link sb-toggle active-group" id="toggleServices" data-filter="all">
                    <i class="fas fa-print sb-icon"></i> SERVICES
                    <i class="fas fa-chevron-down sb-chevron" id="chevServices"></i>
                </button>

                <div class="sb-sub" id="subServices">
                    <?php echo $this->renderServicesSub(); ?>
                </div>
            </div>

            <!-- About Us link -->
            <a href="?page=about" class="sb-link">
                <i class="fas fa-info-circle sb-icon"></i> ABOUT US
            </a>

        </nav>
        <?php
        return ob_get_clean();
    }

    // ── All category sub-items ────────────────────────────────────────────
    private function renderServicesSub(): string {
        // Each entry: [filter-id, icon, label, items array]
        $categories = [
            [
                'id'    => 'sublimation',
                'icon'  => 'fa-fire',
                'label' => 'SUBLIMATION',
                'items' => ['Jersey', 'T-Shirt', 'Short', 'Warmer', 'Jogging Pants', 'Long Sleeve', 'Polo Shirt'],
            ],
            [
                'id'    => 'uniform',
                'icon'  => 'fa-user-tie',
                'label' => 'UNIFORM',
                'items' => ['School Uniform', 'Office Uniform', 'Professional Uniform'],
            ],
            [
                'id'    => 'tarpaulin',
                'icon'  => 'fa-scroll',
                'label' => 'TARPAULIN',
                'items' => ['Birthday Tarpaulin', 'Graduation Tarpaulin', 'Congratulation Tarpaulin'],
            ],
            [
                'id'    => 'mug',
                'icon'  => 'fa-mug-hot',
                'label' => 'SUBLIMATION MUGS',
                'items' => ['Sublimation Mug'],
            ],
            [
                'id'    => 'lanyard',
                'icon'  => 'fa-id-card',
                'label' => 'ID LANYARDS',
                'items' => ['School ID Lanyard', 'Office ID Lanyard', 'Professional ID Lanyard'],
            ],
            [
                'id'    => 'stitching',
                'icon'  => 'fa-cut',
                'label' => 'CUSTOM STITCHING',
                'items' => ['Custom Stitched T-Shirt'],
            ],
            [
                'id'    => 'sticker',
                'icon'  => 'fa-tags',
                'label' => 'STICKERS & DECALS',
                'items' => ['Motorcycle Decals', 'Truck Decals', 'Car Decals'],
            ],
        ];

        ob_start();
        foreach ($categories as $cat):
            $toggleId = 'toggle_' . $cat['id'];
            $subId    = 'sub_'    . $cat['id'];
            $chevId   = 'chev_'   . $cat['id'];
        ?>
            <!-- <?php echo $cat['label']; ?> -->
            <button class="sb-sub-toggle" id="<?php echo $toggleId; ?>" data-filter="<?php echo $cat['id']; ?>">
                <i class="fas <?php echo $cat['icon']; ?>"></i>
                <?php echo $cat['label']; ?>
                <i class="fas fa-chevron-down sb-chevron" id="<?php echo $chevId; ?>"></i>
            </button>
            <div class="sb-sub-items" id="<?php echo $subId; ?>">
                <?php foreach ($cat['items'] as $item): ?>
                    <a href="#" class="sb-item" data-filter="item" data-name="<?php echo htmlspecialchars($item); ?>">
                        <?php echo htmlspecialchars($item); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php
        endforeach;
        return ob_get_clean();
    }
}
?>
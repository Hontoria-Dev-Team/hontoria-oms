<?php
require_once __DIR__ . '/../home_components/Component.php';

/**
 * Services Sidebar Component
 * Renders sidebar navigation with filtering
 */
class ServicesSidebarComponent extends Component {
    
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

    private function renderSearch(): string {
        return <<<HTML
        <div class="sb-search">
            <i class="fas fa-search sb-search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search Here" class="sb-search-input"/>
        </div>
        HTML;
    }

    private function renderNav(): string {
        ob_start();
        ?>
        <nav class="sb-nav">
            <!-- HOME -->
            <a href="?page=home" class="sb-link">
                <i class="fas fa-home sb-icon"></i> HOME
            </a>

            <!-- SERVICES with sub-items -->
            <div class="sb-group">
                <button class="sb-link sb-toggle active-group" id="toggleServices" data-filter="all">
                    <i class="fas fa-print sb-icon"></i> SERVICES
                    <i class="fas fa-chevron-down sb-chevron" id="chevServices"></i>
                </button>
                <div class="sb-sub" id="subServices">

                    <!-- SUBLIMATION -->
                    <button class="sb-sub-toggle" id="toggleSublim" data-filter="sublimation">
                        <i class="fas fa-tshirt"></i> SUBLIMATION
                        <i class="fas fa-chevron-down sb-chevron" id="chevSublim"></i>
                    </button>
                    <div class="sb-sub-items" id="subSublim">
                        <a href="#" class="sb-item" data-filter="item" data-name="Jersey">Jersey</a>
                        <a href="#" class="sb-item" data-filter="item" data-name="T-Shirt">T-shirt</a>
                        <a href="#" class="sb-item" data-filter="item" data-name="Short">Short</a>
                        <a href="#" class="sb-item" data-filter="item" data-name="Warmer">Warmer</a>
                        <a href="#" class="sb-item" data-filter="item" data-name="Jogging Pants">Jogging Pants</a>
                    </div>

                    <!-- TARPAULIN -->
                    <button class="sb-sub-toggle" id="toggleTarp" data-filter="tarpaulin">
                        <i class="fas fa-flag"></i> TARPAULIN
                        <i class="fas fa-chevron-down sb-chevron" id="chevTarp"></i>
                    </button>
                    <div class="sb-sub-items" id="subTarp">
                        <a href="#" class="sb-item" data-filter="item" data-name="Birthday Tarpaulin">Birthday</a>
                        <a href="#" class="sb-item" data-filter="item" data-name="Graduation Tarpaulin">Graduation</a>
                        <a href="#" class="sb-item" data-filter="item" data-name="Congratulation Tarpaulin">Congratulation</a>
                    </div>
                </div>
            </div>

            <a href="?page=profile" class="sb-link">
                <i class="fas fa-briefcase sb-icon"></i> PROFILE
            </a>
            <a href="?page=about" class="sb-link">
                <i class="fas fa-info-circle sb-icon"></i> ABOUT US
            </a>
        </nav>
        <?php
        return ob_get_clean();
    }
}
?>
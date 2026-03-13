<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * AboutUsSidebarComponent.php
 * Single Responsibility: renders the sticky sidebar navigation for About Us sections.
 */
class AboutUsSidebarComponent extends \Component {

    public function render(): string {
        $navItems = [
            ['id' => 'history',   'icon' => 'fa-book-open',      'label' => 'Our Story'],
            ['id' => 'workplace', 'icon' => 'fa-store',           'label' => 'Inside the Shop'],
            ['id' => 'products',  'icon' => 'fa-tshirt',          'label' => 'Our Products'],
            ['id' => 'location',  'icon' => 'fa-map-marker-alt',  'label' => 'Location'],
            ['id' => 'owner',     'icon' => 'fa-user-tie',        'label' => 'The Owner'],
            ['id' => 'employees', 'icon' => 'fa-users',           'label' => 'Our Team'],
        ];

        ob_start();
        ?>
        <aside class="about-sidebar" id="aboutSidebar">
            <div class="about-sidebar-brand">
                <span class="about-sidebar-title">ABOUT US</span>
            </div>
            <nav class="about-sidebar-nav">
                <?php foreach ($navItems as $item): ?>
                    <a href="#<?php echo $item['id']; ?>"
                       class="about-sidebar-link"
                       data-section="<?php echo $item['id']; ?>">
                        <i class="fas <?php echo $item['icon']; ?> about-sidebar-icon"></i>
                        <span><?php echo $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <?php
        return ob_get_clean();
    }
}
?>
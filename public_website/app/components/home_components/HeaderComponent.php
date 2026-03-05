<?php
require_once __DIR__ . '/Component.php';

/**
 * Header Component
 * Renders navigation, logo, and order bar
 */
class HeaderComponent extends \Component {

    public function render(): string {
        $logoPath = $this->get('logoPath', 'logo.jpg');
        $fbLink = $this->get('fbLink', 'https://www.facebook.com/jhong.hontoria.3');
        $navItems = $this->get('navItems', []);

        ob_start();
        ?>
        <header class="header" id="header">
            <div class="header-inner">
                <?php echo $this->renderLogo($logoPath); ?>
                <?php echo $this->renderNav($navItems); ?>
                <?php echo $this->renderHeaderRight(); ?>
            </div>
            <?php echo $this->renderOrderBar($fbLink); ?>
            <?php echo $this->renderMobileNav($navItems, $fbLink); ?>
            <div class="overlay" id="overlay"></div>
        </header>
        <?php
        return ob_get_clean();
    }

    private function renderLogo(string $logoPath): string {
        ob_start();
        ?>
        <div class="logo">
            <img src="<?php echo $this->escape($logoPath); ?>" alt="Hontoria Logo" class="logo-img"
                 onerror="this.style.display='none'"/>
            <div class="logo-text">
                <span class="logo-name">HONTORIA</span>
                <span class="logo-sub">PRINTING SERVICES</span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderNav(array $navItems): string {
        ob_start();
        ?>
        <nav class="nav-desktop">
            <?php foreach ($navItems as $i => $item): ?>
                <?php if ($i > 0): ?><span class="nav-sep">|</span><?php endif; ?>
                <a href="<?php echo $this->escape($item['url']); ?>"
                    class="nav-link <?php echo $item['active'] ? 'active' : ''; ?>">
                    <?php echo $this->escape($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
        return ob_get_clean();
    }

    private function renderHeaderRight(): string {
        ob_start();
        ?>
        <div class="header-right">
            <!-- LOG IN button removed as per client request -->
            <button class="hamburger" id="hamburger" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderOrderBar(string $fbLink): string {
        ob_start();
        ?>
        <div class="order-bar">
            <span class="order-bar-text">
                <i class="fab fa-facebook-f"></i>
                FOR ORDERS CLICK HERE:
                <a href="<?php echo $this->escape($fbLink); ?>" target="_blank" class="fb-link">
                    <i class="fab fa-facebook"></i> FACEBOOK PAGE
                </a>
            </span>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderMobileNav(array $navItems, string $fbLink): string {
        ob_start();
        ?>
        <div class="mobile-nav" id="mobileNav">
            <button class="close-nav" id="closeNav"><i class="fas fa-times"></i></button>
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo $this->escape($item['url']); ?>" class="mob-link">
                    <?php echo $this->escape($item['label']); ?>
                </a>
            <?php endforeach; ?>
            <!-- LOG IN removed from mobile nav as well -->
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
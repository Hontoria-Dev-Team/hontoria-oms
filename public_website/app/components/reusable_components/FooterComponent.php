<?php
require_once __DIR__ . '/Component.php';

/**
 * FooterComponent.php
 * Location: app/components/reusable_components/FooterComponent.php
 *
 * Shared across all pages (Home, Services, About Us).
 * Styles live in: public/css/shared.css
 * Scripts live in: public/js/shared.js
 */
class FooterComponent extends \Component {

    public function render(): string {
        $logoPath = $this->get('logoPath', 'logo.jpg');
        $fbLink   = $this->get('fbLink',   'https://www.facebook.com/jhong.hontoria.3');
        $address  = $this->get('address',  'Feeder Road 2, Brgy. Tibal-og Santo tomas, Davao del Norte');
        $navLinks = $this->get('navLinks', []);

        ob_start();
        ?>
        <footer class="footer">
            <div class="footer-inner">
                <?php echo $this->renderBrand($logoPath); ?>
                <?php echo $this->renderLinks($navLinks); ?>
                <?php echo $this->renderContact($fbLink, $address); ?>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Hontoria Printing Services &mdash; Designed by: <strong>VICB</strong></p>
            </div>
        </footer>
        <?php
        return ob_get_clean();
    }

    private function renderBrand(string $logoPath): string {
        ob_start();
        ?>
        <div class="footer-brand">
            <div class="footer-logo">
                <img src="<?php echo $this->escape($logoPath); ?>" alt="Hontoria Logo"
                     class="footer-logo-img" onerror="this.style.display='none'"/>
                <div>
                    <span class="f-name">HONTORIA</span>
                    <span class="f-sub">PRINTING SERVICES</span>
                </div>
            </div>
            <p class="footer-tagline">Quality. Affordable. Fast.</p>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderLinks(array $navLinks): string {
        ob_start();
        ?>
        <div class="footer-links">
            <h5>Navigation</h5>
            <?php foreach ($navLinks as $link): ?>
                <a href="<?php echo $this->escape($link['url']); ?>">
                    <?php echo $this->escape($link['label']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderContact(string $fbLink, string $address): string {
        ob_start();
        ?>
        <div class="footer-contact">
            <h5>Connect With Us</h5>
            <a href="<?php echo $this->escape($fbLink); ?>" target="_blank">
                <i class="fab fa-facebook-f"></i> Facebook Page
            </a>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo $this->escape($address); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
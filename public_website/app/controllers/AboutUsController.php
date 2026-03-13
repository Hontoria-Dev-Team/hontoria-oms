<?php
/**
 * AboutUsController.php
 * Renders the About Us page and handles employee AJAX requests.
 */
class AboutUsController {

    public function index() {

        // ── Handle AJAX employee add/remove requests ──────────────────────
        if (isset($_POST['action'])) {
            require_once __DIR__ . '/../components/aboutus_components/EmployeeManager.php';
            (new \EmployeeManager())->handleRequest();
            return; // Stop here — no HTML needed for AJAX
        }

        // ── Load config ───────────────────────────────────────────────────
        $config = \Config::getInstance();

        $logo    = $config->get('site.logoPath');
        $fb      = $config->get('site.fbLink');
        $address = $config->get('site.address');
        $baseUrl = $config->get('paths.base_url');

        // ── Render components ─────────────────────────────────────────────
        $header  = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $this->getNavigation()]))->render();
        $content = (new \AboutUsComponent([]))->render();
        $footer  = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $this->getNavigation()]))->render();

        $sharedCss = $baseUrl . '/css/shared.css';
        $sharedJs  = $baseUrl . '/js/shared.js';
        $css = $baseUrl . '/css/aboutus.css';
        $js  = $baseUrl . '/js/aboutus.js';

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>About Us — <?php echo htmlspecialchars($config->get('site.name')); ?></title>

            <!-- Favicon -->
            <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo); ?>"/>

            <!-- Font Awesome Icons -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

            <!-- 1. Shared styles (header, footer, variables) -->
            <link rel="stylesheet" href="<?php echo htmlspecialchars($sharedCss); ?>"/>
            <!-- 2. About Us-specific styles -->
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>"/>
        </head>
        <body>

            <!-- Header & Navigation -->
            <?php echo $header; ?>

            <main>
                <!-- All About Us sections -->
                <?php echo $content; ?>
            </main>

            <!-- Footer -->
            <?php echo $footer; ?>

            <!-- 1. Shared JS (mobile nav) -->
            <script src="<?php echo htmlspecialchars($sharedJs); ?>"></script>
            <!-- 2. About Us-specific JS -->
            <script src="<?php echo htmlspecialchars($js); ?>"></script>

        </body>
        </html>
        <?php
    }

    // ── Navigation items (ABOUT US marked as active) ──────────────────────
    private function getNavigation(): array {
        return [
            ['label' => 'HOME',     'url' => '?page=home',     'active' => false],
            ['label' => 'SERVICES', 'url' => '?page=services', 'active' => false],
            ['label' => 'ABOUT US', 'url' => '?page=about',    'active' => true],
        ];
    }
}
?>
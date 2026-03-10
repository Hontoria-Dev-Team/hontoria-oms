<?php
/**
 * AboutUsController.php
 * CSS: shared.css → aboutus.css
 * JS:  shared.js  → aboutus.js
 */
class AboutUsController {

    public function index() {

        // ── Handle AJAX employee requests ─────────────────────────────────
        if (isset($_POST['action'])) {
            require_once __DIR__ . '/../components/aboutus_components/EmployeeManager.php';
            (new \EmployeeManager())->handleRequest();
            return;
        }

        $config  = \Config::getInstance();
        $logo    = $config->get('site.logoPath');
        $fb      = $config->get('site.fbLink');
        $address = $config->get('site.address');

        $header  = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $this->getNavigation()]))->render();
        $content = (new \AboutUsComponent([]))->render();
        $footer  = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $this->getNavigation()]))->render();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>About Us — <?php echo htmlspecialchars($config->get('site.name')); ?></title>
            <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo); ?>"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
            <link rel="stylesheet" href="css/shared.css"/>
            <link rel="stylesheet" href="css/aboutus.css"/>
        </head>
        <body>
            <?php echo $header; ?>
            <main>
                <?php echo $content; ?>
            </main>
            <?php echo $footer; ?>
            <script src="js/shared.js"></script>
            <script src="js/aboutus.js"></script>
        </body>
        </html>
        <?php
    }

    private function getNavigation(): array {
        return [
            ['label' => 'HOME',     'url' => '?page=home',     'active' => false],
            ['label' => 'SERVICES', 'url' => '?page=services', 'active' => false],
            ['label' => 'ABOUT US', 'url' => '?page=about',    'active' => true],
        ];
    }
}
?>
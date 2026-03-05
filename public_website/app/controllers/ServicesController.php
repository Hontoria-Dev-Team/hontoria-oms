<?php
/**
 * ServicesController.php
 * Renders the services page by assembling all page components.
 */
class ServicesController {

    public function index() {

        // ── 1. Load site-wide configuration (singleton) ──────────────────
        $config = \Config::getInstance();

        // ── 2. Shortcut variables for cleaner code below ─────────────────
        $logo    = $config->get('site.logoPath');
        $fb      = $config->get('site.fbLink');
        $address = $config->get('site.address');
        $baseUrl = $config->get('paths.base_url');

        // ── 3. Load all products from the database/model ─────────────────
        $products = \product::getAllProducts();

        // ── 4. Render each section into a variable ────────────────────────
        $header  = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $this->getNavigation()]))->render();
        $sidebar = (new \ServicesSidebarComponent(['logoPath' => $logo]))->render();
        $content = (new \ServicesContentComponent(['products' => $products, 'fbLink' => $fb]))->render();
        $footer  = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $this->getNavigation()]))->render();
        $modal   = (new \ServicesModalComponent(['fbLink' => $fb]))->render();

        // ── 5. CSS and JS paths ───────────────────────────────────────────
        $css = $baseUrl . '/css/services.css';
        $js  = $baseUrl . '/js/services.js';

        // ── 6. Output the full HTML page ──────────────────────────────────
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Services — <?php echo htmlspecialchars($config->get('site.name')); ?></title>

            <!-- Favicon -->
            <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo); ?>"/>

            <!-- Google Fonts -->
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>

            <!-- Font Awesome Icons -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

            <!-- Page Stylesheet -->
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>"/>
        </head>
        <body>

            <!-- Header & Navigation -->
            <?php echo $header; ?>

            <!-- Page Body: Sidebar + Main Content -->
            <div class="page-body">

                <!-- Sidebar Filter / Navigation -->
                <?php echo $sidebar; ?>

                <!-- Products / Services Content -->
                <?php echo $content; ?>

            </div>

            <!-- Footer -->
            <?php echo $footer; ?>

            <!-- Order Modal (hidden by default, triggered by JS) -->
            <?php echo $modal; ?>

            <!-- Page Scripts -->
            <script src="<?php echo htmlspecialchars($js); ?>"></script>

        </body>
        </html>
        <?php
    }

    // ── Navigation items (SERVICES marked as active) ──────────────────────
    private function getNavigation(): array {
        return [
            ['label' => 'HOME',     'url' => '?page=home',     'active' => false],
            ['label' => 'SERVICES', 'url' => '?page=services', 'active' => true],
            ['label' => 'ABOUT US', 'url' => '?page=about',    'active' => false],
        ];
    }
}
?>
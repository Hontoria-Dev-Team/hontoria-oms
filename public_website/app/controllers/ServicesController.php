<?php
/**
 * ServicesController.php
 * CSS: shared.css → services.css
 * JS:  shared.js  → services.js
 */
class ServicesController {

    public function index() {
        $config   = \Config::getInstance();
        $logo     = $config->get('site.logoPath');
        $fb       = $config->get('site.fbLink');
        $address  = $config->get('site.address');
        $baseUrl  = $config->get('paths.base_url');
        $products = \product::getAllProducts();

        $header  = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $this->getNavigation()]))->render();
        $sidebar = (new \ServicesSidebarComponent(['logoPath' => $logo]))->render();
        $content = (new \ServicesContentComponent(['products' => $products, 'fbLink' => $fb]))->render();
        $footer  = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $this->getNavigation()]))->render();
        $modal   = (new \ServicesModalComponent(['fbLink' => $fb]))->render();

        $sharedCss = $baseUrl . '/css/shared.css';
        $sharedJs  = $baseUrl . '/js/shared.js';
        $pageCss   = $baseUrl . '/css/services.css';
        $pageJs    = $baseUrl . '/js/services.js';
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Services — <?php echo htmlspecialchars($config->get('site.name')); ?></title>
            <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo); ?>"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($sharedCss); ?>"/>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($pageCss); ?>"/>
        </head>
        <body>
            <?php echo $header; ?>
            <div class="page-body">
                <?php echo $sidebar; ?>
                <?php echo $content; ?>
            </div>
            <?php echo $footer; ?>
            <?php echo $modal; ?>
            <script src="<?php echo htmlspecialchars($sharedJs); ?>"></script>
            <script src="<?php echo htmlspecialchars($pageJs); ?>"></script>
        </body>
        </html>
        <?php
    }

    private function getNavigation(): array {
        return [
            ['label' => 'HOME',     'url' => '?page=home',     'active' => false],
            ['label' => 'SERVICES', 'url' => '?page=services', 'active' => true],
            ['label' => 'ABOUT US', 'url' => '?page=about',    'active' => false],
        ];
    }
}
?>
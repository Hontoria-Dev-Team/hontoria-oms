<?php
/**
 * HomeController.php
 * CSS: shared.css → home.css
 * JS:  shared.js  → home.js
 */
class HomeController {

    public function index() {
        $config  = \Config::getInstance();
        $logo    = $config->get('site.logoPath');
        $fb      = $config->get('site.fbLink');
        $address = $config->get('site.address');
        $nav     = $config->get('navigation');
        $baseUrl = $config->get('paths.base_url');

        $header   = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $nav]))->render();
        $hero     = (new \HeroComponent(['fbLink' => $fb, 'slides' => []]))->render();
        $services = (new \ServicesPreviewComponent(['services' => []]))->render();
        $whyUs    = (new \WhyUsComponent(['items' => []]))->render();
        $footer   = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $nav]))->render();

        $sharedCss = $baseUrl . '/css/shared.css';
        $sharedJs  = $baseUrl . '/js/shared.js';
        $pageCss   = $config->get('paths.css');   // home.css (already stored in config)
        $pageJs    = $config->get('paths.js');    // home.js
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title><?php echo htmlspecialchars($config->get('site.name')); ?></title>
            <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo); ?>"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($sharedCss); ?>"/>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($pageCss); ?>"/>
        </head>
        <body>
            <?php echo $header; ?>
            <main>
                <?php echo $hero; ?>
                <?php echo $services; ?>
                <?php echo $whyUs; ?>
            </main>
            <?php echo $footer; ?>
            <script src="<?php echo htmlspecialchars($sharedJs); ?>"></script>
            <script src="<?php echo htmlspecialchars($pageJs); ?>"></script>
        </body>
        </html>
        <?php
    }
}
?>
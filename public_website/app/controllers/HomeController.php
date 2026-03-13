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

        $header   = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $nav]))->render();
        $hero     = (new \HeroComponent(['fbLink' => $fb, 'slides' => []]))->render();
        $services = (new \ServicesPreviewComponent(['services' => []]))->render();
        $whyUs    = (new \WhyUsComponent(['items' => []]))->render();
        $footer   = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $nav]))->render();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title><?php echo htmlspecialchars($config->get('site.name')); ?></title>
            <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logo); ?>"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
            <link rel="stylesheet" href="css/shared.css"/>
            <link rel="stylesheet" href="css/home.css"/>
        </head>
        <body>
            <?php echo $header; ?>
            <main>
                <?php echo $hero; ?>
                <?php echo $services; ?>
                <?php echo $whyUs; ?>
            </main>
            <?php echo $footer; ?>
            <script src="js/shared.js"></script>
            <script src="js/home.js"></script>
        </body>
        </html>
        <?php
    }
}
?>
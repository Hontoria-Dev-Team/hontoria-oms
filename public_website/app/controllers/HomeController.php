<?php
/**
 * HomeController.php
 * Renders the homepage by assembling all page components.
 */
class HomeController {

    public function index() {

        // ── 1. Load site-wide configuration (singleton) ──────────────────
        $config = \Config::getInstance();

        // ── 2. Shortcut variables for cleaner code below ─────────────────
        $logo    = $config->get('site.logoPath');
        $fb      = $config->get('site.fbLink');
        $address = $config->get('site.address');
        $nav     = $config->get('navigation');

        // ── 3. Render each section into a variable ────────────────────────
        $header   = (new \HeaderComponent(['logoPath' => $logo, 'fbLink' => $fb, 'navItems' => $nav]))->render();
        $hero     = (new \HeroComponent(['fbLink' => $fb, 'slides' => []]))->render();
        $services = (new \ServicesPreviewComponent(['services' => []]))->render();
        $whyUs    = (new \WhyUsComponent(['items' => []]))->render();
        $footer   = (new \FooterComponent(['logoPath' => $logo, 'fbLink' => $fb, 'address' => $address, 'navLinks' => $nav]))->render();

        // ── 4. CSS and JS paths ───────────────────────────────────────────
        $css = $config->get('paths.css');
        $js  = $config->get('paths.js');

        // ── 5. Output the full HTML page ──────────────────────────────────
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title><?php echo htmlspecialchars($config->get('site.name')); ?></title>

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

            <main>
                <!-- Hero / Banner Section -->
                <?php echo $hero; ?>

                <!-- Services Preview Section -->
                <?php echo $services; ?>

                <!-- Why Choose Us Section -->
                <?php echo $whyUs; ?>
            </main>

            <!-- Footer -->
            <?php echo $footer; ?>

            <!-- Page Scripts -->
            <script src="<?php echo htmlspecialchars($js); ?>"></script>

        </body>
        </html>
        <?php
    }
}
?>
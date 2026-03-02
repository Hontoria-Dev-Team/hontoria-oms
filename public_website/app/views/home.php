<?php
/**
 * Hontoria Printing Services - Homepage View
 * Direct HTML rendering using component classes
 */

// Initialize configuration
$config = \Config::getInstance();

// Get configuration data
$siteName = $config->get('site.name');
$logoPath = $config->get('site.logoPath');
$fbLink = $config->get('site.fbLink');
$address = $config->get('site.address');
$navItems = $config->get('navigation');

// Initialize components to get their rendered HTML
$headerComponent = new \HeaderComponent([
    'logoPath' => $logoPath,
    'fbLink' => $fbLink,
    'navItems' => $navItems
]);

$heroComponent = new \HeroComponent([
    'fbLink' => $fbLink,
    'slides' => []
]);

$servicesComponent = new \ServicesPreviewComponent([
    'services' => []
]);

$whyUsComponent = new \WhyUsComponent([
    'items' => []
]);

$footerComponent = new \FooterComponent([
    'logoPath' => $logoPath,
    'fbLink' => $fbLink,
    'address' => $address,
    'navLinks' => $navItems
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <link rel="icon" type="image/jpeg" href="<?php echo htmlspecialchars($logoPath); ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config->get('paths.css')); ?>"/>
</head>
<body>

<?php echo $headerComponent->render(); ?>

<main>
    <?php echo $heroComponent->render(); ?>
    <?php echo $servicesComponent->render(); ?>
    <?php echo $whyUsComponent->render(); ?>
</main>

<?php echo $footerComponent->render(); ?>

<script src="<?php echo htmlspecialchars($config->get('paths.js')); ?>"></script>

</body>
</html>
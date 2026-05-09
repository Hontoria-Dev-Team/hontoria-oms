<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * CategoryConfig.php
 * Builds $sharedCategories from $servicesCatalog passed by PublicC.php.
 * Used by ServicesSidebarComponents.php to render the sidebar navigation.
 */

// $servicesCatalog is passed from PublicC::showServicesPage() via ServicesPage.php
$servicesCatalog  = $servicesCatalog ?? [];
$sharedCategories = [];

foreach ($servicesCatalog as $service) {
    if ($service['isActive'] == 0) continue;
    $sharedCategories[] = [
        'id'    => strtolower(str_replace(' ', '-', e($service['name']))),
        'label' => strtoupper(e($service['name'])),
        'icon'  => 'fa-print',
        'items' => array_map(function ($name) {
            return e($name);
        }, array_column($service['subservices'], 'name')),
    ];
}

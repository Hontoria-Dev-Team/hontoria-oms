<?php

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
        'id'    => strtolower(str_replace(' ', '-', $service['name'])),
        'label' => strtoupper($service['name']),
        'icon'  => 'fa-print',
        'items' => array_column($service['subservices'], 'name'),
    ];
}

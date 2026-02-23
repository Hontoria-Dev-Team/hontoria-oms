<?php


/**
 * Hontoria Printing Services - Homepage View
 * OOP Implementation with SOLID Principles
 */

// Initialize configuration (Singleton)
$config = \Config::getInstance();

// Create page builder (Dependency Inversion)
$page = new \PageBuilder();

// Set page metadata
$page->setMeta([
    'title' => $config->get('site.name')
]);

// Add CSS
$page->addCSS('/public/css/home.css');

// Build page from components (Open/Closed Principle - easy to add/remove)
$page
    // Header with navigation
    ->addComponent(new \HeaderComponent([
        'logoPath' => $config->get('site.logoPath'),
        'fbLink' => $config->get('site.fbLink'),
        'navItems' => $config->get('navigation')
    ]))
    
    // Main content wrapper start
    ->addComponent(new class extends \Component {
        public function render(): string {
            return '<main>';
        }
    })
    
    // Hero section with carousel
    ->addComponent(new \HeroComponent([
        'fbLink' => $config->get('site.fbLink'),
        'slides' => [] // Empty = use defaults, or pass custom slides
    ]))
    
    // Services preview
    ->addComponent(new \ServicesPreviewComponent([
        'services' => [] // Empty = use defaults
    ]))
    
    // Why choose us
    ->addComponent(new \WhyUsComponent([
        'items' => [] // Empty = use defaults
    ]))
    
    // Main content wrapper end
    ->addComponent(new class extends \Component {
        public function render(): string {
            return '</main>';
        }
    })
    
    // Footer
    ->addComponent(new \FooterComponent([
        'logoPath' => $config->get('site.logoPath'),
        'fbLink' => $config->get('site.fbLink'),
        'address' => $config->get('site.address'),
        'navLinks' => $config->get('navigation')
    ]));

// Add JavaScript
$page->addJS('/public/js/home.js');

// Render and output the complete page
echo $page->build();
?>
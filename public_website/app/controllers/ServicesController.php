<?php
/**
 * Services Controller
 * Handles services page rendering
 */
class ServicesController {
    
    public function index() {
        // Get configuration
        $config = \Config::getInstance();

        // Get all products from model
        $products = \product::getAllProducts();

        // Create page builder
        $page = new \PageBuilder();

        // Set page metadata
        $page->setMeta([
            'title' => 'Services — ' . $config->get('site.name')
        ]);

        // Add CSS
        $page->addCSS($config->get('paths.base_url') . '/css/services.css');

        // Build page
        $page
            // Header
            ->addComponent(new \HeaderComponent([
                'logoPath' => $config->get('site.logoPath'),
                'fbLink' => $config->get('site.fbLink'),
                'navItems' => $this->getNavigation()
            ]))
            
            // Page body wrapper start
            ->addComponent(new class extends \Component {
                public function render(): string {
                    return '<div class="page-body">';
                }
            })
            
            // Sidebar
            ->addComponent(new \ServicesSidebarComponent([
                'logoPath' => $config->get('site.logoPath')
            ]))
            
            // Main content
            ->addComponent(new \ServicesContentComponent([
                'products' => $products,
                'fbLink' => $config->get('site.fbLink')
            ]))
            
            // Page body wrapper end
            ->addComponent(new class extends \Component {
                public function render(): string {
                    return '</div>';
                }
            })
            
            // Footer
            ->addComponent(new \FooterComponent([
                'logoPath' => $config->get('site.logoPath'),
                'fbLink' => $config->get('site.fbLink'),
                'address' => $config->get('site.address'),
                'navLinks' => $this->getNavigation()
            ]))
            
            // Modal
            ->addComponent(new \ServicesModalComponent([
                'fbLink' => $config->get('site.fbLink')
            ]));

        // Add JavaScript
        $page->addJS($config->get('paths.base_url') . '/js/services.js');

        // Render
        echo $page->build();
    }

    /**
     * Get navigation with services marked as active
     */
    private function getNavigation(): array {
        return [
            ['label' => 'HOME', 'url' => '?page=home', 'active' => false],
            ['label' => 'SERVICES', 'url' => '?page=services', 'active' => true],
            ['label' => 'PROFILE', 'url' => '?page=profile', 'active' => false],
            ['label' => 'ABOUT US', 'url' => '?page=about', 'active' => false]
        ];
    }
}
?>
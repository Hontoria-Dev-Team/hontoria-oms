<?php
/**
 * Home Controller
 * Handles homepage rendering
 */
class HomeController {
    
    public function index() {
        // Render homepage directly
        $this->renderHomePage();
    }
    
    private function renderHomePage() {
        // Initialize configuration (Singleton)
        $config = \Config::getInstance();

        // Create page builder
        $page = new \PageBuilder();

        // Set page metadata
        $page->setMeta([
            'title' => $config->get('site.name')
        ]);

        // Add CSS - USE CONFIG PATH (it's already absolute!)
        $page->addCSS($config->get('paths.css'));

        // Build page from components
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
                'slides' => []
            ]))
            
            // Services preview
            ->addComponent(new \ServicesPreviewComponent([
                'services' => []
            ]))
            
            // Why choose us
            ->addComponent(new \WhyUsComponent([
                'items' => []
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

        // Add JavaScript - USE CONFIG PATH
        $page->addJS($config->get('paths.js'));

        // Render and output the complete page
        echo $page->build();
    }
}
?>
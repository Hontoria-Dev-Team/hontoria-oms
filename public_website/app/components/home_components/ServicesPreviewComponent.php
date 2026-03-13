<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * Services Preview \Component
 * Renders service cards on homepage
 */
class ServicesPreviewComponent extends \Component {
    
    public function render(): string {
        $services = $this->get('services', []);
        if (empty($services)) {
            $services = $this->getDefaultServices();
        }
        
        ob_start();
        ?>
        <section class="services-preview">
            <div class="section-header">
                <p class="section-eyebrow">WHAT WE OFFER</p>
                <h2 class="section-title">Our Services</h2>
                <div class="section-line"></div>
            </div>

            <div class="services-grid">
                <?php foreach ($services as $i => $service): ?>
                    <?php echo $this->renderServiceCard($service, $i * 150); ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    private function renderServiceCard(array $service, int $delay): string {
        ob_start();
        ?>
        <div class="service-card" data-delay="<?php echo $delay; ?>">
            <div class="card-icon-wrap <?php echo $this->escape($service['iconClass']); ?>">
                <i class="fas <?php echo $this->escape($service['icon']); ?>"></i>
            </div>
            <h3 class="card-title"><?php echo $this->escape($service['title']); ?></h3>
            
            <?php if (!empty($service['items'])): ?>
                <ul class="card-list">
                    <?php foreach ($service['items'] as $item): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo $this->escape($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="card-desc"><?php echo $this->escape($service['description']); ?></p>
            <?php endif; ?>
            
            <a href="<?php echo $this->escape($service['link']); ?>" class="card-btn">
                <?php echo $this->escape($service['btnText']); ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    private function getDefaultServices(): array {
        return [
            [
                'title' => 'Sublimation',
                'icon' => 'fa-tshirt',
                'iconClass' => 'sublim-icon',
                'items' => ['Jersey', 'T-Shirt', 'Short', 'Warmer', 'Jogging Pants'],
                'link' => '../ServicesPage/services.html#sublimation',
                'btnText' => 'See Pricing'
            ],
            [
                'title' => 'Tarpaulin',
                'icon' => 'fa-flag',
                'iconClass' => 'tarp-icon',
                'items' => ['Birthday', 'Graduation', 'Congratulation'],
                'link' => '../ServicesPage/services.html#tarpaulin',
                'btnText' => 'See Pricing'
            ],
            [
                'title' => 'Our Portfolio',
                'icon' => 'fa-briefcase',
                'iconClass' => 'port-icon',
                'description' => 'Browse our completed works and see the quality and creativity we put into every single order.',
                'link' => '../ProfilePage/profile.html',
                'btnText' => 'View Portfolio'
            ]
        ];
    }
}
?>
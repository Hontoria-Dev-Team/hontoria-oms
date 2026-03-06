<?php
require_once __DIR__ . '/\Component.php';

/**
 * Why Us \Component
 * Renders reasons to choose Hontoria
 */
class WhyUsComponent extends \Component {
    
    public function render(): string {
        $items = $this->get('items', []);
        if (empty($items)) {
            $items = $this->getDefaultItems();
        }
        
        ob_start();
        ?>
        <section class="why-us">
            <div class="why-bg"></div>
            <div class="why-content">
                <div class="section-header dark">
                    <p class="section-eyebrow">WHY HONTORIA?</p>
                    <h2 class="section-title">Built on Trust &amp; Quality</h2>
                    <div class="section-line"></div>
                </div>
                <div class="why-grid">
                    <?php foreach ($items as $item): ?>
                        <?php echo $this->renderItem($item); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    private function renderItem(array $item): string {
        ob_start();
        ?>
        <div class="why-item">
            <div class="why-icon"><i class="fas <?php echo $this->escape($item['icon']); ?>"></i></div>
            <h4><?php echo $this->escape($item['title']); ?></h4>
            <p><?php echo $this->escape($item['description']); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }

    private function getDefaultItems(): array {
        return [
            [
                'icon' => 'fa-medal',
                'title' => 'Premium Quality',
                'description' => 'We use only the best materials and inks for long-lasting, vibrant prints.'
            ],
            [
                'icon' => 'fa-bolt',
                'title' => 'Fast Turnaround',
                'description' => 'Quick processing so your orders are ready when you need them.'
            ],
            [
                'icon' => 'fa-peso-sign',
                'title' => 'Affordable Prices',
                'description' => 'Competitive pricing without compromising on the quality you deserve.'
            ],
            [
                'icon' => 'fa-headset',
                'title' => 'Easy Ordering',
                'description' => 'Simply reach out via Facebook or use our inquiry form — hassle-free.'
            ]
        ];
    }
}
?>
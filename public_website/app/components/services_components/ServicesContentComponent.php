<?php
require_once __DIR__ . '/../home_components/Component.php';

/**
 * Services Content Component
 * Renders product sections and cards
 */
class ServicesContentComponent extends \Component {
    
    public function render(): string {
        $products = $this->get('products', []);
        $fbLink = $this->get('fbLink', 'https://www.facebook.com/jhong.hontoria.3');
        
        // Group products by category
        $sublimation = array_filter($products, fn($p) => $p->getCategory() === 'sublimation');
        $tarpaulin = array_filter($products, fn($p) => $p->getCategory() === 'tarpaulin');
        
        ob_start();
        ?>
        <main class="content">
            <!-- PAGE TITLE -->
            <div class="content-header">
                <h1 class="page-title">SERVICES</h1>
                <p class="page-sub" id="filterLabel">Click any product to view details &amp; pricing</p>
            </div>

            <!-- SUBLIMATION SECTION -->
            <?php echo $this->renderSection('sublimation', 'SUBLIMATION', 'fa-tshirt', 'sublim-badge', $sublimation, $fbLink); ?>

            <!-- TARPAULIN SECTION -->
            <?php echo $this->renderSection('tarpaulin', 'TARPAULIN', 'fa-flag', 'tarp-badge', $tarpaulin, $fbLink); ?>
        </main>
        <?php
        return ob_get_clean();
    }

    private function renderSection(string $id, string $title, string $icon, string $badgeClass, array $products, string $fbLink): string {
        ob_start();
        ?>
        <section class="product-section" id="<?php echo $this->escape($id); ?>">
            <div class="section-label">
                <span class="section-badge <?php echo $this->escape($badgeClass); ?>">
                    <i class="fas <?php echo $this->escape($icon); ?>"></i> <?php echo $this->escape($title); ?>
                </span>
                <div class="section-line"></div>
            </div>

            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php echo $this->renderProductCard($product, $fbLink); ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    private function renderProductCard(Product $product, string $fbLink): string {
        ob_start();
        ?>
        <div class="product-card" 
             id="<?php echo $this->escape($product->getId()); ?>" 
             data-category="<?php echo $this->escape($product->getCategory()); ?>" 
             data-name="<?php echo $this->escape($product->getName()); ?>">
            
            <div class="card-img <?php echo $this->escape($product->getCategoryClass()); ?>">
                <div class="img-placeholder">
                    <i class="fas <?php echo $this->escape($product->getIcon()); ?> ph-icon"></i>
                    <span class="ph-label"><?php echo $this->escape($product->getName()); ?></span>
                    <span class="ph-hint">Add photo here</span>
                </div>
                <div class="card-overlay">
                    <button class="view-btn">View Details <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            
            <div class="card-info">
                <h3 class="card-name"><?php echo $this->escape($product->getName()); ?></h3>
                <p class="card-desc"><?php echo $this->escape($product->getDescription()); ?></p>
                <a href="<?php echo $this->escape($fbLink); ?>" target="_blank" class="order-btn">
                    <i class="fab fa-facebook-f"></i> Order Now
                </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
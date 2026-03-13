<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * ServicesContentComponent.php
 * Renders product sections and cards.
 *
 * HOW TO ADD A NEW CATEGORY:
 * 1. Add the product to product.php with the correct category id
 * 2. Add the category to CategoryConfig.php
 * 3. Nothing else needed — this component reads categories dynamically
 */
class ServicesContentComponent extends \Component {

    public function render(): string {
        $products = $this->get('products', []);
        $fbLink   = $this->get('fbLink', 'https://www.facebook.com/jhong.hontoria.3');

        // ── Group products by category dynamically ────────────────────────
        // No need to hardcode categories here — just group whatever comes in
        $grouped = [];
        foreach ($products as $product) {
            $grouped[$product->getCategory()][] = $product;
        }

        // ── Category display config (label, icon, badge class) ────────────
        $categoryMeta = [
            'sublimation' => ['label' => 'SUBLIMATION',       'icon' => 'fa-fire',        'badge' => 'sublim-badge'],
            'uniform'     => ['label' => 'UNIFORM',           'icon' => 'fa-user-tie',     'badge' => 'uniform-badge'],
            'tarpaulin'   => ['label' => 'TARPAULIN',         'icon' => 'fa-scroll',       'badge' => 'tarp-badge'],
            'mug'         => ['label' => 'SUBLIMATION MUGS',  'icon' => 'fa-mug-hot',      'badge' => 'mug-badge'],
            'lanyard'     => ['label' => 'ID LANYARDS',       'icon' => 'fa-id-card',      'badge' => 'lanyard-badge'],
            'stitching'   => ['label' => 'CUSTOM STITCHING',  'icon' => 'fa-cut',          'badge' => 'stitching-badge'],
            'sticker'     => ['label' => 'STICKERS & DECALS', 'icon' => 'fa-tags',         'badge' => 'sticker-badge'],
        ];

        ob_start();
        ?>
        <main class="content">

            <!-- PAGE TITLE -->
            <div class="content-header">
                <h1 class="page-title">SERVICES</h1>
                <p class="page-sub" id="filterLabel">Click any product to view details &amp; pricing</p>
            </div>

            <?php foreach ($categoryMeta as $categoryId => $meta): ?>
                <?php if (!empty($grouped[$categoryId])): ?>
                    <?php echo $this->renderSection(
                        $categoryId,
                        $meta['label'],
                        $meta['icon'],
                        $meta['badge'],
                        $grouped[$categoryId],
                        $fbLink
                    ); ?>
                <?php endif; ?>
            <?php endforeach; ?>

        </main>
        <?php
        return ob_get_clean();
    }

    // ── Render one category section ───────────────────────────────────────
    private function renderSection(string $id, string $title, string $icon, string $badgeClass, array $products, string $fbLink): string {
        ob_start();
        ?>
        <section class="product-section" id="<?php echo $this->escape($id); ?>">
            <div class="section-label">
                <span class="section-badge <?php echo $this->escape($badgeClass); ?>">
                    <i class="fas <?php echo $this->escape($icon); ?>"></i>
                    <?php echo $this->escape($title); ?>
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

    // ── Render one product card ───────────────────────────────────────────
    private function renderProductCard(product $product, string $fbLink): string {
        ob_start();
        ?>
        <div class="product-card"
             id="<?php echo $this->escape($product->getId()); ?>"
             data-category="<?php echo $this->escape($product->getCategory()); ?>"
             data-name="<?php echo $this->escape($product->getName()); ?>">

            <!-- Card image / photo -->
            <div class="card-img <?php echo $this->escape($product->getCategoryClass()); ?>">
                <?php if ($product->getPhoto()): ?>
                    <img src="<?php echo $this->escape($product->getPhoto()); ?>"
                         alt="<?php echo $this->escape($product->getName()); ?>"
                         class="card-photo"/>
                <?php else: ?>
                    <div class="img-placeholder">
                        <i class="fas <?php echo $this->escape($product->getIcon()); ?> ph-icon"></i>
                        <span class="ph-label"><?php echo $this->escape($product->getName()); ?></span>
                        <span class="ph-hint">Photo coming soon</span>
                    </div>
                <?php endif; ?>
                <div class="card-overlay">
                    <button class="view-btn"><i class="fas fa-eye"></i> View Details</button>
                </div>
            </div>

            <!-- Card info -->
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
<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * ServicesContentComponent.php
 * Renders product sections and cards.
 */
class ServicesContentComponent extends \Component {

    public function render(): string {
        $products = $this->get('products', []);
        $fbLink   = $this->get('fbLink', 'https://www.facebook.com/jhong.hontoria.3');

        $grouped = [];
        foreach ($products as $product) {
            $grouped[$product->getCategory()][] = $product;
        }

        $categoryMeta = [
            'sublimation' => ['label' => 'SUBLIMATION',              'icon' => 'fa-fire',        'badge' => 'sublim-badge'],
            'uniform'     => ['label' => 'UNIFORM',                  'icon' => 'fa-user-tie',    'badge' => 'uniform-badge'],
            'tarpaulin'   => ['label' => 'CUSTOMIZE TARPAULIN',      'icon' => 'fa-scroll',      'badge' => 'tarp-badge'],
            'mug'         => ['label' => 'CUSTOMIZE MUGS & TUMBLER', 'icon' => 'fa-mug-hot',     'badge' => 'mug-badge'],
            'lanyard'     => ['label' => 'CUSTOMIZE LANYARD',        'icon' => 'fa-id-card',     'badge' => 'lanyard-badge'],
            'stitching'   => ['label' => 'CUSTOM STITCHING',         'icon' => 'fa-cut',         'badge' => 'stitching-badge'],
            'sticker'     => ['label' => 'STICKERS & DECALS',        'icon' => 'fa-tags',        'badge' => 'sticker-badge'],
        ];

        ob_start();
        ?>
        <main class="content">

            <div class="content-header">
                <h1 class="page-title">SERVICES</h1>
                <p class="page-sub" id="filterLabel">Click any product to view details &amp; pricing</p>
                <div class="mobile-search">
                    <i class="fas fa-search mobile-search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search products..." class="mobile-search-input"/>
                </div>
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

    private function renderProductCard(product $product, string $fbLink): string {
        ob_start();
        ?>
        <div class="product-card"
             id="<?php echo $this->escape($product->getId()); ?>"
             data-category="<?php echo $this->escape($product->getCategory()); ?>"
             data-name="<?php echo $this->escape($product->getName()); ?>"
             data-price="<?php echo $product->getPrice(); ?>"
             data-photos="<?php echo $this->escape(json_encode($product->getPhotos())); ?>"
             data-variants="<?php echo $this->escape(json_encode($product->getVariants())); ?>">

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
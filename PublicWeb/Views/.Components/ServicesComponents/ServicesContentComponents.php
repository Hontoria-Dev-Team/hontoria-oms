<?php
$fbLink = 'https://www.facebook.com/jhong.hontoria.3';

// Group products dynamically
$grouped = [];
foreach ($products as $product) {
    $cat = is_object($product) ? $product->getCategory() : ($product['category'] ?? 'uncategorized');
    $grouped[$cat][] = $product;
}
?>

<main class="content">
    <div class="content-header">
        <h1 class="page-title">SERVICES</h1>
        <p class="page-sub" id="filterLabel">Click any product to view details &amp; pricing</p>
        <div class="mobile-search">
            <i class="fas fa-search mobile-search-icon"></i>
            <input type="text" id="searchInputMobile" placeholder="Search products..." class="mobile-search-input"/>
        </div>
    </div>

    <?php foreach ($sharedCategories as $categoryId => $meta): ?>
        <?php if (!empty($grouped[$categoryId])): ?>
            
            <section class="product-section" id="<?php echo htmlspecialchars($categoryId); ?>">
                <div class="section-label">
                    <span class="section-badge <?php echo htmlspecialchars($meta['badge']); ?>">
                        <i class="fas <?php echo htmlspecialchars($meta['icon']); ?>"></i>
                        <?php echo htmlspecialchars($meta['label']); ?>
                    </span>
                    <div class="section-line"></div>
                </div>
                
                <div class="product-grid">
                    <?php foreach ($grouped[$categoryId] as $prod): ?>
                        <?php 
                            $pId    = is_object($prod) ? $prod->getId() : ($prod['id'] ?? '');
                            $pCat   = is_object($prod) ? $prod->getCategory() : ($prod['category'] ?? '');
                            $pName  = is_object($prod) ? $prod->getName() : ($prod['name'] ?? 'Unknown');
                            $pPrice = is_object($prod) ? $prod->getPrice() : ($prod['price'] ?? 0);
                            $pPhoto = is_object($prod) ? $prod->getPhoto() : ($prod['photo'] ?? '');
                            $pDesc  = is_object($prod) ? $prod->getDescription() : ($prod['description'] ?? '');
                            $pIcon  = is_object($prod) ? $prod->getIcon() : ($prod['icon'] ?? 'fa-box');
                            $pClass = is_object($prod) ? $prod->getCategoryClass() : ($prod['categoryClass'] ?? '');
                            $pPhots = json_encode(is_object($prod) ? $prod->getPhotos() : ($prod['photos'] ?? []));
                            $pVars  = json_encode(is_object($prod) ? $prod->getVariants() : ($prod['variants'] ?? []));
                        ?>
                        
                        <div class="product-card"
                             id="<?php echo htmlspecialchars($pId); ?>"
                             data-category="<?php echo htmlspecialchars($pCat); ?>"
                             data-name="<?php echo htmlspecialchars($pName); ?>"
                             data-price="<?php echo htmlspecialchars($pPrice); ?>"
                             data-photos="<?php echo htmlspecialchars($pPhots); ?>"
                             data-variants="<?php echo htmlspecialchars($pVars); ?>">

                            <div class="card-img <?php echo htmlspecialchars($pClass); ?>">
                                <?php if (!empty($pPhoto)): ?>
                                    <img src="<?php echo htmlspecialchars($pPhoto); ?>" alt="<?php echo htmlspecialchars($pName); ?>" class="card-photo"/>
                                <?php else: ?>
                                    <div class="img-placeholder">
                                        <i class="fas <?php echo htmlspecialchars($pIcon); ?> ph-icon"></i>
                                        <span class="ph-label"><?php echo htmlspecialchars($pName); ?></span>
                                        <span class="ph-hint">Photo coming soon</span>
                                    </div>
                                <?php endif; ?>
                                <div class="card-overlay">
                                    <button class="view-btn"><i class="fas fa-eye"></i> View Details</button>
                                </div>
                            </div>

                            <div class="card-info">
                                <h3 class="card-name"><?php echo htmlspecialchars($pName); ?></h3>
                                <p class="card-desc"><?php echo htmlspecialchars($pDesc); ?></p>
                                <a href="<?php echo htmlspecialchars($fbLink); ?>" target="_blank" class="order-btn">
                                    <i class="fab fa-facebook-f"></i> Order Now
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </section>

        <?php endif; ?>
    <?php endforeach; ?>
</main>
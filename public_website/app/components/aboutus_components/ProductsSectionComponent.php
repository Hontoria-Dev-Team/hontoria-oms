<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * ProductsSectionComponent.php
 * Single Responsibility: renders the Our Products section only.
 */
class ProductsSectionComponent extends \Component {

    public function render(): string {
        ob_start();
        ?>
        <section class="section products-section" id="products">
            <div class="section-inner">
                <p class="section-eyebrow">What We Offer</p>
                <h2 class="section-title">Our Products</h2>
                <div class="section-line"></div>
                <p class="section-desc" style="margin-bottom:36px">Quality products at prices that won't break the bank.</p>

                <div class="products-grid">
                    <div class="product-pill"><i class="fas fa-tshirt"></i><span>Sublimation Shirts</span></div>
                    <div class="product-pill"><i class="fas fa-user-tie"></i><span>Jersey / Uniform</span></div>
                    <div class="product-pill"><i class="fas fa-scroll"></i><span>Tarpaulin Banners</span></div>
                    <div class="product-pill"><i class="fas fa-mug-hot"></i><span>Sublimation Mugs</span></div>
                    <div class="product-pill"><i class="fas fa-id-card"></i><span>ID Lanyards</span></div>
                    <div class="product-pill"><i class="fas fa-ruler-combined"></i><span>Custom Stitching</span></div>
                    <div class="product-pill"><i class="fas fa-image"></i><span>Photo Prints</span></div>
                    <div class="product-pill"><i class="fas fa-tags"></i><span>Stickers &amp; Labels</span></div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
?>
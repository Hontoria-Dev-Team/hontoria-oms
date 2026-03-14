<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * HistorySectionComponent.php
 * Single Responsibility: renders the Our Story / History section only.
 */
class HistorySectionComponent extends \Component {

    public function render(): string {
        ob_start();
        ?>
        <section class="section history-section" id="history">
            <div class="section-inner">
                <div class="history-layout">

                    <!-- Text side -->
                    <div class="history-text">
                        <p class="section-eyebrow">Our Story</p>
                        <h2 class="section-title">How It All Started</h2>
                        <div class="section-line"></div>
                        <p>Hontoria Printing Services was founded with a simple mission: deliver high-quality, affordable printed products to the community of Davao del Norte. What started as a small home-based printing setup has grown into a full-service print shop trusted by hundreds of customers.</p>
                        <p>From humble beginnings with a single heat press machine, we have expanded our services to include sublimation printing, tarpaulin printing, and custom tailoring — all under one roof.</p>
                        <p>Every product that leaves our shop carries the same commitment to quality and craftsmanship that our founder built this business on.</p>
                    </div>

                    <!-- Image side -->
                    <div class="history-img-wrap">
                        <div class="history-img-placeholder">
                            <i class="fas fa-store"></i>
                            <span>SHOP PHOTO</span>
                        </div>
                        <div class="history-badge">
                            <span>Est.</span>
                            2018
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
?>
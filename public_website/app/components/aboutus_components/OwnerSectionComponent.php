<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * OwnerSectionComponent.php
 * Single Responsibility: renders the Meet the Owner section only.
 */
class OwnerSectionComponent extends \Component {

    public function render(): string {
        ob_start();
        ?>
        <section class="section owner-section" id="owner">
            <div class="section-inner">
                <div class="owner-layout">

                    <!-- Owner photo -->
                    <div class="owner-img-wrap">
                        <div class="owner-img-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="owner-tag">Owner</span>
                    </div>

                    <!-- Owner info -->
                    <div class="owner-info">
                        <p class="section-eyebrow">Meet the Owner</p>
                        <div class="owner-name">Jhong Hontoria</div>
                        <div class="owner-title">Founder &amp; Owner</div>
                        <p class="owner-bio">Jhong built Hontoria Printing Services from the ground up with a passion for quality craftsmanship and a drive to serve the local community. With years of hands-on experience in printing and garment production, he leads the team with dedication and an eye for detail.</p>
                    </div>

                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
?>
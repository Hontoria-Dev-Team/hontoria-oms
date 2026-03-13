<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * WorkplaceSectionComponent.php
 * Single Responsibility: renders the Inside the Shop / Workplace Areas section only.
 */
class WorkplaceSectionComponent extends \Component {

    public function render(): string {
        ob_start();
        ?>
        <section class="section workplace-section" id="workplace">
            <div class="section-inner">
                <p class="section-eyebrow">Our Workspace</p>
                <h2 class="section-title">Inside the Shop</h2>
                <div class="section-line"></div>
                <p class="section-desc" style="margin-bottom:36px">Our shop is organized into dedicated areas so every job gets the focused attention it deserves.</p>

                <div class="areas-grid">

                    <div class="area-card">
                        <div class="area-img-placeholder area-layout">
                            <i class="fas fa-desktop"></i>
                            <span>LAYOUT AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Layout Area</div>
                            <div class="area-desc">Where our designers bring your ideas to life using professional design software. Every print starts here.</div>
                        </div>
                    </div>

                    <div class="area-card">
                        <div class="area-img-placeholder area-print">
                            <i class="fas fa-print"></i>
                            <span>PRINTING AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Printing Area</div>
                            <div class="area-desc">Equipped with high-quality printers capable of producing vivid, sharp, and durable prints on multiple media.</div>
                        </div>
                    </div>

                    <div class="area-card">
                        <div class="area-img-placeholder area-tailor">
                            <i class="fas fa-cut"></i>
                            <span>TAILOR AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Tailor Area</div>
                            <div class="area-desc">Our skilled tailors handle custom garments and alterations with precision and care for every stitch.</div>
                        </div>
                    </div>

                    <div class="area-card">
                        <div class="area-img-placeholder area-heat">
                            <i class="fas fa-fire"></i>
                            <span>HEATPRESS AREA</span>
                        </div>
                        <div class="area-info">
                            <div class="area-name">Heatpress Area</div>
                            <div class="area-desc">Sublimation and heat transfer printing station where designs are permanently bonded onto fabric and hard surfaces.</div>
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
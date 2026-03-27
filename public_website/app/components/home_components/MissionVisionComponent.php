<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * MissionVisionComponent.php
 * Renders the Mission & Vision section for the home page.
 * Placed after WhyUsComponent.
 */
class MissionVisionComponent extends \Component {

    public function render(): string {
        ob_start();
        ?>
        <section class="mv-section" id="mission-vision">

            <!-- Decorative background -->
            <div class="mv-bg">
                <div class="mv-bg-line mv-line-1"></div>
                <div class="mv-bg-line mv-line-2"></div>
                <div class="mv-bg-circle"></div>
            </div>

            <div class="mv-inner">

                <!-- Section eyebrow -->
                <div class="mv-eyebrow">
                    <span class="mv-eyebrow-line"></span>
                    <span class="mv-eyebrow-text">WHO WE ARE</span>
                    <span class="mv-eyebrow-line"></span>
                </div>

                <!-- Cards row -->
                <div class="mv-cards">

                    <!-- MISSION -->
                    <div class="mv-card mv-card--mission">
                        <div class="mv-card-number">01</div>
                        <div class="mv-card-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="mv-card-title">Our Mission</h3>
                        <div class="mv-card-line"></div>
                        <p class="mv-card-text">
                            To provide high-quality, affordable, and creative printing services
                            with fast turnaround and excellent customer service — bringing
                            every vision to life with precision and passion.
                        </p>
                        <div class="mv-card-tag">
                            <i class="fas fa-check"></i> Quality First
                        </div>
                    </div>

                    <!-- Divider accent -->
                    <div class="mv-divider">
                        <div class="mv-divider-icon">
                            <i class="fas fa-print"></i>
                        </div>
                    </div>

                    <!-- VISION -->
                    <div class="mv-card mv-card--vision">
                        <div class="mv-card-number">02</div>
                        <div class="mv-card-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="mv-card-title">Our Vision</h3>
                        <div class="mv-card-line"></div>
                        <p class="mv-card-text">
                            To be a trusted and leading printing service provider known
                            for quality, reliability, and customer satisfaction — the go-to
                            printing partner in every community we serve.
                        </p>
                        <div class="mv-card-tag">
                            <i class="fas fa-star"></i> Built to Last
                        </div>
                    </div>

                </div>

                <!-- Bottom quote -->
                <div class="mv-quote">
                    <span class="mv-quote-mark">"</span>
                    <p class="mv-quote-text">Every print tells a story. We make sure yours stands out.</p>
                    <span class="mv-quote-mark">"</span>
                </div>

            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
?>
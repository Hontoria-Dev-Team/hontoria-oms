<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * HeroSectionComponent.php
 * Single Responsibility: renders the About Us page hero banner only.
 */
class HeroSectionComponent extends \Component {

    public function render(): string {
        ob_start();
        ?>
        <div class="page-hero">
            <p class="page-hero-eyebrow">Who We Are</p>
            <h1 class="page-hero-title">About <span>Hontoria</span></h1>
            <p class="page-hero-sub">A family-built printing business rooted in quality, creativity, and community.</p>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
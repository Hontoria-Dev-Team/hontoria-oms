<?php
require_once __DIR__ . '/Component.php';

/**
 * Hero Component
 * Renders hero section with carousel and CTAs
 */
class HeroComponent extends \Component {
    
    public function render(): string {
        $fbLink = $this->get('fbLink', 'https://www.facebook.com/jhong.hontoria.3');
        $slides = $this->get('slides', []);
        
        ob_start();
        ?>
        <section class="hero" id="home">
            <?php echo $this->renderBackground(); ?>
            
            <div class="hero-content">
                <?php echo $this->renderBadge(); ?>
                <?php echo $this->renderTitle(); ?>
                <?php echo $this->renderDescription(); ?>
                <?php echo $this->renderCTAs($fbLink); ?>
            </div>

            <?php echo $this->renderCarousel($slides); ?>
        </section>
        <?php
        return ob_get_clean();
    }

    private function renderBackground(): string {
        return <<<HTML
        <div class="hero-bg">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
            <div class="grid-lines"></div>
        </div>
        HTML;
    }

    private function renderBadge(): string {
        return <<<HTML
        <div class="hero-badge">
            <span class="badge-dot"></span>
            Quality · Affordable · Fast
        </div>
        HTML;
    }

    private function renderTitle(): string {
        return <<<HTML
        <h1 class="hero-title">
            <span class="title-line line-1">YOUR VISION,</span>
            <span class="title-line line-2">PERFECTLY <span class="accent-text">PRINTED.</span></span>
        </h1>
        HTML;
    }

    private function renderDescription(): string {
        return <<<HTML
        <p class="hero-desc">
            From custom sublimation jerseys to vibrant tarpaulins —
            we bring your designs to life with precision and speed.
        </p>
        HTML;
    }

    private function renderCTAs(string $fbLink): string {
        ob_start();
        ?>
        <div class="hero-actions">
            <a href="?page=services" class="btn-primary">
                <i class="fas fa-layer-group"></i> VIEW SERVICES
            </a>
            <a href="<?php echo $this->escape($fbLink); ?>" target="_blank" class="btn-secondary">
                <i class="fab fa-facebook-f"></i> ORDER VIA FACEBOOK
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderCarousel(array $slides): string {
        if (empty($slides)) {
            $slides = $this->getDefaultSlides();
        }

        ob_start();
        ?>
        <div class="hero-photo">
            <div class="photo-frame">
                <div class="pc-track">
                    <?php foreach ($slides as $i => $slide): ?>
                        <div class="pc-slide <?php echo $i === 0 ? 'active' : ''; ?>">
                            <?php if (!empty($slide['image'])): ?>
                                <img src="<?php echo $this->escape($slide['image']); ?>" 
                                     class="pc-img" alt="<?php echo $this->escape($slide['label']); ?>">
                            <?php else: ?>
                                <div class="pc-placeholder <?php echo $this->escape($slide['bgClass']); ?>">
                                    <i class="fas <?php echo $this->escape($slide['icon']); ?> pc-icon"></i>
                                    <p class="pc-label"><?php echo $this->escape($slide['label']); ?></p>
                                    <span class="pc-hint"><?php echo $this->escape($slide['hint']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="pc-arrow pc-prev" id="pcPrev"><i class="fas fa-chevron-left"></i></button>
                <button class="pc-arrow pc-next" id="pcNext"><i class="fas fa-chevron-right"></i></button>

                <div class="pc-dots">
                    <?php foreach ($slides as $i => $slide): ?>
                        <button class="pc-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-idx="<?php echo $i; ?>"></button>
                    <?php endforeach; ?>
                </div>

                <div class="float-badge badge-sublim"><i class="fas fa-tshirt"></i> Sublimation</div>
                <div class="float-badge badge-tarp"><i class="fas fa-flag"></i> Tarpaulin</div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function getDefaultSlides(): array {
        // Get absolute path for carousel images
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        $publicPath = defined('PUBLIC_PATH') ? PUBLIC_PATH : $_SERVER['DOCUMENT_ROOT'] . $scriptPath;
        $photoDir = $publicPath . '/img/carouselPhoto/';
        
        $slides = [];
        
        // Slide 1 - Use highlight_photo1.jpg
        $slide1Photo = null;
        if (file_exists($photoDir . 'highlight_photo1.jpg')) {
            $slide1Photo = $scriptPath . '/img/carouselPhoto/highlight_photo1.jpg';
        }
        
        $slides[] = [
            'image' => $slide1Photo,
            'bgClass' => 'pc-bg1',
            'icon' => 'fa-image',
            'label' => 'HONTORIA',
            'hint' => 'Quality Printing Services'
        ];
        
        // For other slides
        for ($i = 2; $i <= 4; $i++) {
            $photo = null;
            
            if (is_dir($photoDir)) {
                $files = glob($photoDir . 'slide-' . $i . '.*');
                if (!empty($files)) {
                    $photo = $scriptPath . '/img/carouselPhoto/' . basename($files[0]);
                }
            }
            
            $slides[] = [
                'image' => $photo,
                'bgClass' => 'pc-bg' . $i,
                'icon' => ['fa-tshirt', 'fa-flag', 'fa-star'][$i-2],
                'label' => ['SUBLIMATION', 'TARPAULIN', 'OUR BEST WORK'][$i-2],
                'hint' => ['Jerseys · T-Shirts · Shorts', 'Birthday · Graduation · Congrats', 'Quality prints every time'][$i-2]
            ];
        }
        
        return $slides;
    }
}
?>
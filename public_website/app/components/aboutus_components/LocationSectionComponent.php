<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * LocationSectionComponent.php
 * Single Responsibility: renders the Our Location section only.
 */
class LocationSectionComponent extends \Component {

    public function render(): string {
        $fbLink  = $this->get('fbLink',  'https://www.facebook.com/jhong.hontoria.3');
        $address = $this->get('address', 'Feeder Road 2, Brgy. Tibal-og Santo Tomas, Davao del Norte');

        ob_start();
        ?>
        <section class="section location-section" id="location">
            <div class="section-inner">
                <div class="location-layout">

                    <!-- Live Google Maps Embed -->
                    <div class="location-map-wrap">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!4v1775546378932!6m8!1m7!1sb15YPSZyDOgxicNazUUiMw!2m2!1d7.528266967563585!2d125.6236165603115!3f81.551414!4f0!5f0.7820865974627469"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <!-- Details -->
                    <div class="location-details">
                        <p class="section-eyebrow">Find Us</p>
                        <h2 class="section-title">Our Location</h2>
                        <div class="section-line"></div>

                        <div class="location-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="location-item-text">
                                <strong>Address</strong>
                                <span><?php echo $this->escape($address); ?></span>
                            </div>
                        </div>

                        <div class="location-item">
                            <i class="fab fa-facebook"></i>
                            <div class="location-item-text">
                                <strong>Facebook Page</strong>
                                <span>
                                    <a href="<?php echo $this->escape($fbLink); ?>" target="_blank" style="color:var(--red)">
                                        Hontoria Printing Services
                                    </a>
                                </span>
                            </div>
                        </div>

                        <div class="location-item">
                            <i class="fas fa-clock"></i>
                            <div class="location-item-text">
                                <strong>Business Hours</strong>
                                <span>Monday – Saturday: 8:00 AM – 6:00 PM</span>
                            </div>
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
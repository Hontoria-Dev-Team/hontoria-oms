<?php
require_once __DIR__ . '/../reusable_components/Component.php';

/**
 * ServicesModalComponent.php
 * Renders the product detail modal.
 * Supports variant selector (e.g. White Mug / Magic Mug) with auto price switching.
 */
class ServicesModalComponent extends \Component {

    public function render(): string {
        $fbLink = $this->get('fbLink', 'https://www.facebook.com/jhong.hontoria.3');

        ob_start();
        ?>
        <div class="modal-overlay" id="modalOverlay">
            <div class="modal" id="modal">

                <button class="modal-close" id="modalClose"><i class="fas fa-times"></i></button>

                <div class="modal-left">
                    <div class="modal-main-img" id="modalMainImg">
                        <i class="fas fa-image modal-ph-icon" id="modalPhIcon"></i>
                        <span class="modal-ph-label" id="modalPhLabel"></span>
                    </div>
                    <div class="modal-thumbs" id="modalThumbs">
                        <?php for ($i = 0; $i < 8; $i++): ?>
                            <div class="thumb <?php echo $i === 0 ? 'active' : ''; ?>" data-idx="<?php echo $i; ?>">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="modal-right">
                    <h2 class="modal-title" id="modalTitle">Product Name</h2>

                    <div class="modal-field">
                        <span class="modal-field-label">Product Description:</span>
                        <p class="modal-desc" id="modalDesc"></p>
                    </div>

                    <div class="modal-field">
                        <span class="modal-field-label">Price:</span>
                        <span class="modal-price" id="modalPrice">Contact us for pricing</span>
                    </div>

                    <!-- Variant selector — hidden by default, JS shows it when variants exist -->
                    <div class="modal-variant-row" id="modalVariantRow" style="display:none">
                        <span class="modal-field-label">Type:</span>
                        <select class="modal-variant-select" id="modalVariantSelect"></select>
                    </div>

                    <div class="modal-spacer"></div>

                    <div class="modal-qty-row">
                        <span class="modal-field-label">Quantity:</span>
                        <div class="qty-control">
                            <button class="qty-btn" id="qtyMinus"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input" id="qtyInput" value="1" min="1"/>
                            <button class="qty-btn" id="qtyPlus"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <div class="modal-total-row">
                        <span class="modal-field-label">Estimated Total:</span>
                        <div class="total-display" id="totalDisplay">—</div>
                    </div>

                    <!-- Note for customer -->
                    <div class="modal-field">
                        <span class="modal-field-label">Note:</span>
                        <p class="modal-desc">Screenshot your preferred design and send it directly to our
                        <a href="<?php echo $this->escape($fbLink); ?>" target="_blank" style="color:#CC1A00;font-weight:700">Facebook Messenger</a>.
                        We'll get back to you with the details!</p>
                    </div>

                    <div class="modal-actions">
                        <a href="<?php echo $this->escape($fbLink); ?>" target="_blank" class="modal-order">
                            <i class="fab fa-facebook-messenger"></i> Order via Facebook
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
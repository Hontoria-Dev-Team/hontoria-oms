<?php
$fbLink = 'https://www.facebook.com/jhong.hontoria.3';
?>

<div class="modal-overlay" id="modalOverlay">

    <div class="modal">

        <button class="modal-close" id="modalClose">
            <i class="fas fa-times"></i>
        </button>

        <!-- LEFT -->
        <div class="modal-left">

            <div class="modal-main-img" id="modalMainImg"></div>

            <div class="modal-thumbs" id="modalThumbs"></div>

        </div>

        <!-- RIGHT -->
        <div class="modal-right">

            <h2 class="modal-title" id="modalTitle"></h2>

            <div class="modal-field">
                <span class="modal-field-label">Product Description:</span>
                <p class="modal-desc" id="modalDesc"></p>
            </div>

            <div class="modal-field">
                <span class="modal-field-label">Price:</span>
                <span class="modal-price" id="modalPrice"></span>
            </div>

            <!-- Variant Row (hidden by default) -->
            <div class="modal-field" id="modalVariantRow" style="display:none;">
                <span class="modal-field-label">Variant:</span>
                <select id="modalVariantSelect" class="modal-variant-select"></select>
            </div>

            <div class="modal-qty-row">
                <span class="modal-field-label">Units:</span>
                <div class="qty-control">
                    <button id="qtyMinus">&#8722;</button>
                    <input type="number" id="qtyInput" value="1" min="1">
                    <button id="qtyPlus">&#43;</button>
                </div>
            </div>

            <div class="modal-total-row">
                <span class="modal-field-label">Estimated Total:</span>
                <div id="totalDisplay" class="modal-total-value">&#8212;</div>
            </div>

            <div class="modal-note">
                <span class="modal-field-label">Note:</span>
                <p class="modal-desc">
                    Screenshot your preferred design and send it directly to our
                    <a href="<?php echo htmlspecialchars($fbLink); ?>"
                        target="_blank"
                        class="modal-fb-link">Facebook Messenger</a>.
                    We&rsquo;ll get back to you with the details!
                </p>
            </div>

            <a href="<?php echo htmlspecialchars($fbLink); ?>"
                target="_blank"
                class="modal-order"
                id="orderButton">
                <i class="fab fa-facebook-messenger"></i>
                Order via Facebook
            </a>

        </div>

    </div>

</div>
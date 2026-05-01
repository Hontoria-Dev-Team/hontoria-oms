<?php
$fbLink = 'https://www.facebook.com/jhong.hontoria.3';
$servicesCatalog = $servicesCatalog ?? [];
?>

<main class="content">

    <div class="content-header">
        <h1 class="page-title">SERVICES</h1>
        <p class="page-sub" id="filterLabel">
            Click any product to view details & pricing
        </p>
    </div>

    <?php foreach ($servicesCatalog as $service): ?>

        <section class="product-section"
            id="<?php echo strtolower(str_replace(' ', '-', $service['name'])); ?>">

            <div class="section-label">
                <div class="section-badge sublim-badge">
                    <?php echo strtoupper($service['name']); ?>
                </div>
                <div class="section-line"></div>
            </div>

            <div class="product-grid">

                <?php foreach ($service['subservices'] as $subservice): ?>

                    <div class="product-card"

                        data-name="<?php echo htmlspecialchars($subservice['name']); ?>"

                        data-category="<?php echo htmlspecialchars($service['name']); ?>"

                        data-price="<?php echo htmlspecialchars($subservice['pricePerUnit']); ?>"

                        data-description="<?php echo htmlspecialchars($subservice['description']); ?>"

                        data-photos='<?php echo json_encode(array_map(
                            fn($img) => "../../Storage/SubserviceImages/" . $img['imageName'],
                            $subservice['images'] ?? []
                        )); ?>'>

                        <!-- IMAGE -->
                        <div class="card-img">

                            <?php if (!empty($subservice['images'])): ?>

                                <img
                                    src="../../Storage/SubserviceImages/<?php echo htmlspecialchars($subservice['images'][0]['imageName']); ?>"
                                    class="card-photo">

                            <?php else: ?>

                                <div class="img-placeholder">
                                    <i class="fas fa-image ph-icon"></i>
                                    <span class="ph-label">No Image</span>
                                </div>

                            <?php endif; ?>

                            <!-- VIEW BUTTON -->
                            <div class="card-overlay">
                                <button class="view-btn">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </div>

                        </div>

                        <!-- INFO -->
                        <div class="card-info">

                            <h3 class="card-name">
                                <?php echo htmlspecialchars($subservice['name']); ?>
                            </h3>

                            <p class="card-desc">
                                <?php echo htmlspecialchars($subservice['description']); ?>
                            </p>

                            <a href="<?php echo htmlspecialchars($fbLink); ?>"
                               target="_blank"
                               class="order-btn">
                                Order Now
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endforeach; ?>

</main>
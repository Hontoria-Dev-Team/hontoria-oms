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
        <?php if ($service['isActive'] == 0) continue; ?>

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

                        data-is-active="<?php echo htmlspecialchars($subservice['isActive']); ?>"

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

                            <?php if ($subservice['isActive'] == 0 || $service['isActive'] == 0): ?>
                                <h5 style="color: var(--red); margin-bottom: 8px;">Not Available For Order</h5>
                            <?php endif; ?>

                            <p class="card-desc">
                                <?php echo htmlspecialchars($subservice['description']); ?>
                            </p>

                            <?php if ($subservice['isActive'] == 1 && $service['isActive'] == 1): ?>
                                <a href="<?php echo htmlspecialchars($fbLink); ?>"
                                    target="_blank"
                                    class="order-btn">
                                    Order Now
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endforeach; ?>

    <?php
    $inactiveServices = array_filter($servicesCatalog, fn($s) => $s['isActive'] == 0);
    $count = count($inactiveServices);
    if ($count > 0):
        $names = array_column($inactiveServices, 'name');
        if ($count === 1) {
            $serviceList = '<span style="text-transform: capitalize;">' . $names[0] . '</span>';
        } elseif ($count === 2) {
            $serviceList = '<span style="text-transform: capitalize;">' . $names[0] . '</span> and <span style="text-transform: capitalize;">' . $names[1] . '</span>';
        } else {
            $last = array_pop($names);
            $serviceList = '<span style="text-transform: capitalize;">' . implode('</span>, <span style="text-transform: capitalize;">', $names) . '</span>, and <span style="text-transform: capitalize;">' . $last . '</span>';
        }
    ?>
        <h4 style="opacity: 0.5; font-style: italic; color: #666;">
            We can also do: <?= $serviceList ?>
            but <?= $count === 1 ? 'this service is' : 'these services are' ?> unavailable right now.
        </h4>
    <?php endif; ?>

</main>
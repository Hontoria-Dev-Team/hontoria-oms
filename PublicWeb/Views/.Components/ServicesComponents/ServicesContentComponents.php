<?php
$fbLink = 'https://www.facebook.com/jhong.hontoria.3';
$servicesCatalog = $servicesCatalog ?? [];
?>

<main class="content">
    <div class="content-header">
        <h1 class="page-title">SERVICES</h1>
        <p class="page-sub">Explore our comprehensive printing services</p>
    </div>

    <?php foreach ($servicesCatalog as $service): ?>
        <section class="service-section">
            <div class="service-header">
                <h2 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h2>
                <span class="service-status <?php echo $service['isActive'] ? 'active' : 'inactive'; ?>">
                    <?php echo $service['isActive'] ? 'Active' : 'Inactive'; ?>
                </span>
            </div>

            <?php if (!empty($service['subservices'])): ?>
                <div class="subservices-grid">
                    <?php foreach ($service['subservices'] as $subservice): ?>
                        <div class="subservice-card">
                            <div class="subservice-header">
                                <h3 class="subservice-name"><?php echo htmlspecialchars($subservice['name']); ?></h3>
                                <span class="subservice-status <?php echo $subservice['isActive'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $subservice['isActive'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>

                            <div class="subservice-images">
                                <?php if (!empty($subservice['images'])): ?>
                                    <div class="image-carousel" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        <?php foreach ($subservice['images'] as $image): ?>
                                            <img src="../../Storage/SubserviceImages/<?php echo htmlspecialchars($image['imageName']); ?>"
                                                alt="<?php echo htmlspecialchars($subservice['name']); ?>"
                                                class="subservice-image"
                                                style="width: 100px; height: auto;" />
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>No images available</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="subservice-info">
                                <p class="subservice-description"><?php echo htmlspecialchars($subservice['description']); ?></p>
                                <div class="subservice-footer">
                                    <span class="price">₱<?php echo number_format($subservice['pricePerUnit'], 2); ?>/unit</span>
                                    <a href="<?php echo htmlspecialchars($fbLink); ?>" target="_blank" class="order-btn">
                                        <i class="fab fa-facebook-f"></i> Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-subservices">No subservices available</p>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</main>
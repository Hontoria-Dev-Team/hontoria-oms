<?php
$logoPath = 'img/logo.jpg';
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" class="sb-logo" onerror="this.style.display='none'"/>
        <div class="sb-brand-text">
            <span class="sb-name">HONTORIA</span>
            <span class="sb-name">PRINTING</span>
            <span class="sb-name">SERVICES</span>
        </div>
    </div>

    <div class="sb-search">
        <i class="fas fa-search sb-search-icon"></i>
        <input type="text" id="searchInput" placeholder="Search Here" class="sb-search-input"/>
    </div>

    <nav class="sb-nav">
        <a href="?page=home" class="sb-link">
            <i class="fas fa-home sb-icon"></i> HOME
        </a>
        <div class="sb-group">
            <button class="sb-link sb-toggle" id="toggleServices" data-filter="all">
                <i class="fas fa-print sb-icon"></i> SERVICES
                <i class="fas fa-chevron-down sb-chevron" id="chevServices"></i>
            </button>
            
            <div class="sb-sub" id="subServices">
                <?php foreach ($sharedCategories as $cat): ?>
                    <?php 
                        $toggleId = 'toggle_' . $cat['id'];
                        $subId    = 'sub_'    . $cat['id'];
                        $chevId   = 'chev_'   . $cat['id'];
                    ?>
                    <button class="sb-sub-toggle" id="<?php echo $toggleId; ?>" data-filter="<?php echo $cat['id']; ?>">
                        <i class="fas <?php echo $cat['icon']; ?>"></i>
                        <?php echo $cat['label']; ?>
                        <i class="fas fa-chevron-down sb-chevron" id="<?php echo $chevId; ?>"></i>
                    </button>
                    <div class="sb-sub-items" id="<?php echo $subId; ?>">
                        <?php foreach ($cat['items'] as $item): ?>
                            <a href="#" class="sb-item" data-filter="item" data-name="<?php echo htmlspecialchars($item); ?>">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="?page=about" class="sb-link">
            <i class="fas fa-info-circle sb-icon"></i> ABOUT US
        </a>
    </nav>
</aside>
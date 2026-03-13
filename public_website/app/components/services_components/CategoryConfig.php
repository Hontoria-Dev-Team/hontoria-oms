<?php
/**
 * CategoryConfig.php
 * Single Responsibility: defines all product categories in one place.
 *
 * Used by the sidebar and content components to render category filters.
 * To add a new category: add one entry to the array below.
 * Your backend teammate can use this to build the admin category manager.
 */
class CategoryConfig {

    /**
     * Returns all categories.
     * Each entry has:
     *  - id         : must match the 'category' value used in product.php
     *  - label      : display name shown in sidebar and section headers
     *  - icon       : Font Awesome icon class
     *  - badgeClass : CSS class for the section badge color
     *  - bgClass    : CSS class for card image background (defined in services.css)
     */
    public static function getAll(): array {
        return [
            [
                'id'         => 'sublimation',
                'label'      => 'Sublimation',
                'icon'       => 'fa-fire',
                'badgeClass' => 'sublim-badge',
                'bgClass'    => 'sublim-img',
            ],
            [
                'id'         => 'uniform',
                'label'      => 'Uniform',
                'icon'       => 'fa-user-tie',
                'badgeClass' => 'uniform-badge',
                'bgClass'    => 'uniform-img',
            ],
            [
                'id'         => 'tarpaulin',
                'label'      => 'Tarpaulin',
                'icon'       => 'fa-scroll',
                'badgeClass' => 'tarp-badge',
                'bgClass'    => 'tarp-img',
            ],
            [
                'id'         => 'mug',
                'label'      => 'Sublimation Mugs',
                'icon'       => 'fa-mug-hot',
                'badgeClass' => 'mug-badge',
                'bgClass'    => 'mug-img',
            ],
            [
                'id'         => 'lanyard',
                'label'      => 'ID Lanyards',
                'icon'       => 'fa-id-card',
                'badgeClass' => 'lanyard-badge',
                'bgClass'    => 'lanyard-img',
            ],
            [
                'id'         => 'stitching',
                'label'      => 'Custom Stitching',
                'icon'       => 'fa-cut',
                'badgeClass' => 'stitching-badge',
                'bgClass'    => 'stitching-img',
            ],
            [
                'id'         => 'sticker',
                'label'      => 'Stickers & Decals',
                'icon'       => 'fa-tags',
                'badgeClass' => 'sticker-badge',
                'bgClass'    => 'sticker-img',
            ],
        ];
    }

    /** Get one category by id */
    public static function getById(string $id): ?array {
        foreach (self::getAll() as $cat) {
            if ($cat['id'] === $id) return $cat;
        }
        return null;
    }
}
?>
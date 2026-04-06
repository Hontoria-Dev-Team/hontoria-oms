<?php
/**
 * product.php
 * Single Responsibility: represents ONE product and exposes its data.
 *
 * This file only knows about ONE product at a time.
 * It depends on the three support files to build the full catalog:
 *   - PhotoLibrary.php   → photo arrays per product
 *   - VariantLibrary.php → price variants per product
 *   - ProductCatalog.php → product definitions (id, name, price, etc.)
 */
require_once __DIR__ . '/PhotoLibrary.php';
require_once __DIR__ . '/VariantLibrary.php';
require_once __DIR__ . '/ProductCatalog.php';

class product {

    private string $id;
    private string $name;
    private string $category;
    private string $description;
    private float  $price;
    private string $icon;
    private string $bgGradient;
    private string $photo;
    private array  $photos;
    private array  $variants;

    public function __construct(array $data) {
        $this->id          = $data['id']          ?? '';
        $this->name        = $data['name']        ?? '';
        $this->category    = $data['category']    ?? '';
        $this->description = $data['description'] ?? '';
        $this->price       = (float)($data['price'] ?? 0);
        $this->icon        = $data['icon']        ?? 'fa-image';
        $this->bgGradient  = $data['bgGradient']  ?? 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
        $this->photo       = $data['photo']       ?? '';
        $this->photos      = $data['photos']      ?? [];
        $this->variants    = $data['variants']    ?? [];
    }

    // ── Getters ───────────────────────────────────────────────────────
    public function getId(): string          { return $this->id; }
    public function getName(): string        { return $this->name; }
    public function getCategory(): string    { return $this->category; }
    public function getDescription(): string { return $this->description; }
    public function getPrice(): float        { return $this->price; }
    public function getIcon(): string        { return $this->icon; }
    public function getBgGradient(): string  { return $this->bgGradient; }
    public function getPhoto(): string       { return $this->photo; }
    public function getPhotos(): array       { return $this->photos; }
    public function getVariants(): array     { return $this->variants; }

    public function getPriceFormatted(): string {
        return $this->price > 0
            ? '₱' . number_format($this->price, 2)
            : 'Contact us for pricing';
    }

    public function getCategoryClass(): string {
        $map = [
            'sublimation' => 'sublim-img',
            'tarpaulin'   => 'tarp-img',
            'uniform'     => 'uniform-img',
            'mug'         => 'mug-img',
            'lanyard'     => 'lanyard-img',
            'stitching'   => 'stitching-img',
            'sticker'     => 'sticker-img',
            'sintra'      => 'sintra-img',
            'photoframe'  => 'photoframe-img',
            'refmagnet'   => 'refmagnet-img',
            'plaque'      => 'plaque-img',
        ];
        return $map[$this->category] ?? 'sublim-img';
    }

    // ── Factory — assembles all products from the three libraries ─────
    public static function getAllProducts(): array {
        $photos   = \PhotoLibrary::build();
        $variants = \VariantLibrary::build();
        $products = [];

        foreach (\ProductCatalog::definitions() as $def) {
            [$id, $name, $category, $description, $price, $icon, $bgGradient, $photo] = $def;

            $products[] = new self([
                'id'          => $id,
                'name'        => $name,
                'category'    => $category,
                'description' => $description,
                'price'       => $price,
                'icon'        => $icon,
                'bgGradient'  => $bgGradient,
                'photo'       => $photo,
                'photos'      => $photos[$id]   ?? [],
                'variants'    => $variants[$id] ?? [],
            ]);
        }

        return $products;
    }

    // ── Queries ───────────────────────────────────────────────────────
    public static function getByCategory(string $category): array {
        return array_values(array_filter(
            self::getAllProducts(),
            fn($p) => $p->getCategory() === $category
        ));
    }

    public static function getById(string $id): ?product {
        foreach (self::getAllProducts() as $product) {
            if ($product->getId() === $id) return $product;
        }
        return null;
    }
}
?>
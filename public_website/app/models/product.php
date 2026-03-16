<?php
/**
 * product.php
 * Product Model — Single Responsibility: manages product data only.
 *
 * HOW TO ADD A NEW PRODUCT:
 * 1. Copy one block below, change the values, save.
 * 2. Set 'price' to 0 for "Contact us for pricing"
 * 3. Set 'photo' to the first/main image path
 * 4. Set 'photos' to an array of all gallery image paths
 * 5. Set 'variants' for products with multiple options (e.g. mug types)
 *    e.g. 'variants' => [['name'=>'White Mug','price'=>150],['name'=>'Magic Mug','price'=>200]]
 */
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
    private array  $variants; // For products with multiple price options

    public function __construct(array $data) {
        $this->id          = $data['id']          ?? '';
        $this->name        = $data['name']        ?? '';
        $this->category    = $data['category']    ?? '';
        $this->description = $data['description'] ?? '';
        $this->price       = $data['price']       ?? 0.0;
        $this->icon        = $data['icon']        ?? 'fa-image';
        $this->bgGradient  = $data['bgGradient']  ?? 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
        $this->photo       = $data['photo']       ?? '';
        $this->photos      = $data['photos']      ?? [];
        $this->variants    = $data['variants']    ?? [];
    }

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
        ];
        return $map[$this->category] ?? 'sublim-img';
    }

    public static function getAllProducts(): array {

        // ── Jersey photos (16 photos) ─────────────────────────────────────
        $jerseyPhotos = [];
        for ($i = 1; $i <= 16; $i++) {
            $jerseyPhotos[] = 'img/sublimationPicture/jerseyPicture/jerseyPicture' . $i . '.jpg';
        }

        // ── T-Shirt photos (25 photos) ────────────────────────────────────
        $tshirtPhotos = [];
        for ($i = 1; $i <= 25; $i++) {
            $tshirtPhotos[] = 'img/sublimationPicture/tshirtPicture/tshirtPicture' . $i . '.jpg';
        }

        // ── Longsleeve photos (2 photos) ──────────────────────────────────
        $longsleevePhotos = [];
        for ($i = 1; $i <= 2; $i++) {
            $longsleevePhotos[] = 'img/sublimationPicture/longsleveePicture/longslevee' . $i . '.jpg';
        }

        // ── Polo Shirt photos (10 photos) ─────────────────────────────────
        $poloshirtPhotos = [];
        for ($i = 1; $i <= 10; $i++) {
            $poloshirtPhotos[] = 'img/sublimationPicture/poloshirtPicture/polo' . $i . '.jpg';
        }

        return [

            // ── SUBLIMATION ───────────────────────────────────────────────
            new product([
                'id'          => 'jersey',
                'name'        => 'Jersey',
                'category'    => 'sublimation',
                'description' => 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',
                'price'       => 300,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => 'img/sublimationPicture/jerseyPicture/jerseyPicture1.jpg',
                'photos'      => $jerseyPhotos,
                'variants'    => [],
            ]),
            new product([
                'id'          => 'tshirt',
                'name'        => 'T-Shirt',
                'category'    => 'sublimation',
                'description' => 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',
                'price'       => 300,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => 'img/sublimationPicture/tshirtPicture/tshirtPicture1.jpg',
                'photos'      => $tshirtPhotos,
                'variants'    => [],
            ]),
            new product([
                'id'          => 'short',
                'name'        => 'Short',
                'category'    => 'sublimation',
                'description' => 'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.',
                'price'       => 0,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'warmer',
                'name'        => 'Warmer',
                'category'    => 'sublimation',
                'description' => 'Sublimation warmers for players and athletes. Keeps you warm while looking professional.',
                'price'       => 0,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'joggingpants',
                'name'        => 'Jogging Pants',
                'category'    => 'sublimation',
                'description' => 'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching.',
                'price'       => 0,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'longsleeve',
                'name'        => 'Long Sleeve',
                'category'    => 'sublimation',
                'description' => 'Full sublimation long sleeve shirts with vibrant custom designs. Perfect for teams, events, and everyday wear.',
                'price'       => 400,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => 'img/sublimationPicture/longsleveePicture/longslevee1.jpg',
                'photos'      => $longsleevePhotos,
                'variants'    => [],
            ]),
            new product([
                'id'          => 'poloshirt',
                'name'        => 'Polo Shirt',
                'category'    => 'sublimation',
                'description' => 'Custom sublimation polo shirts with full color printing. Great for corporate events, teams, and casual wear.',
                'price'       => 430,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => 'img/sublimationPicture/poloshirtPicture/polo1.jpg',
                'photos'      => $poloshirtPhotos,
                'variants'    => [],
            ]),

            // ── UNIFORM ───────────────────────────────────────────────────
            new product([
                'id'          => 'school-uniform',
                'name'        => 'School Uniform',
                'category'    => 'uniform',
                'description' => 'Custom-made school uniforms tailored to your school\'s specifications. Durable, comfortable, and neat.',
                'price'       => 0,
                'icon'        => 'fa-user-graduate',
                'bgGradient'  => 'linear-gradient(135deg,#e8f0ff,#c8d8ff)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'office-uniform',
                'name'        => 'Office Uniform',
                'category'    => 'uniform',
                'description' => 'Professional office uniforms tailored for a sharp, consistent look across your entire team.',
                'price'       => 0,
                'icon'        => 'fa-briefcase',
                'bgGradient'  => 'linear-gradient(135deg,#e8f0ff,#c8d8ff)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'professional-uniform',
                'name'        => 'Professional Uniform',
                'category'    => 'uniform',
                'description' => 'High-quality professional uniforms for healthcare, hospitality, and other industries.',
                'price'       => 0,
                'icon'        => 'fa-user-tie',
                'bgGradient'  => 'linear-gradient(135deg,#e8f0ff,#c8d8ff)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),

            // ── TARPAULIN ─────────────────────────────────────────────────
            new product([
                'id'          => 'birthday',
                'name'        => 'Birthday Tarpaulin',
                'category'    => 'tarpaulin',
                'description' => 'Beautiful custom birthday tarpaulins. Any size, any design — bold and colorful.',
                'price'       => 0,
                'icon'        => 'fa-birthday-cake',
                'bgGradient'  => 'linear-gradient(135deg,#ffe0e0,#ffb3b3)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'graduation',
                'name'        => 'Graduation Tarpaulin',
                'category'    => 'tarpaulin',
                'description' => 'Celebrate achievements with stunning graduation tarpaulins. Custom designs that make the moment unforgettable.',
                'price'       => 0,
                'icon'        => 'fa-graduation-cap',
                'bgGradient'  => 'linear-gradient(135deg,#ffe0e0,#ffb3b3)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'congratulation',
                'name'        => 'Congratulation Tarpaulin',
                'category'    => 'tarpaulin',
                'description' => 'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.',
                'price'       => 0,
                'icon'        => 'fa-star',
                'bgGradient'  => 'linear-gradient(135deg,#ffe0e0,#ffb3b3)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),

            // ── SUBLIMATION MUGS ──────────────────────────────────────────
            new product([
                'id'          => 'mug',
                'name'        => 'Sublimation Mug',
                'category'    => 'mug',
                'description' => 'Full-wrap sublimation printed mugs with your custom design. Perfect for gifts, souvenirs, and corporate giveaways.',
                'price'       => 150,  // Default price (White Mug)
                'icon'        => 'fa-mug-hot',
                'bgGradient'  => 'linear-gradient(135deg,#fff3e0,#ffe0b2)',
                'photo'       => 'img/sublimationMug/mug/mug1.jpg',
                'photos'      => [
                    'img/sublimationMug/mug/mug1.jpg',
                    'img/sublimationMug/mug/mug2.jpg',
                    'img/sublimationMug/mug/mug3.jpg',
                    'img/sublimationMug/mug/mug4.jpg',
                ],
                'variants'    => [
                    ['name' => 'White Mug',  'price' => 150],
                    ['name' => 'Magic Mug',  'price' => 200],
                ],
            ]),

            // ── ID LANYARDS ───────────────────────────────────────────────
            new product([
                'id'          => 'school-id',
                'name'        => 'School ID Lanyard',
                'category'    => 'lanyard',
                'description' => 'Custom printed school ID lanyards with your school logo and colors. Durable and comfortable.',
                'price'       => 0,
                'icon'        => 'fa-id-card',
                'bgGradient'  => 'linear-gradient(135deg,#f3e5f5,#e1bee7)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'office-id',
                'name'        => 'Office ID Lanyard',
                'category'    => 'lanyard',
                'description' => 'Professional office ID lanyards customized with your company branding.',
                'price'       => 0,
                'icon'        => 'fa-id-badge',
                'bgGradient'  => 'linear-gradient(135deg,#f3e5f5,#e1bee7)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'professional-id',
                'name'        => 'Professional ID Lanyard',
                'category'    => 'lanyard',
                'description' => 'High-quality lanyards for professionals, events, and conferences. Custom design and colors.',
                'price'       => 0,
                'icon'        => 'fa-id-card-alt',
                'bgGradient'  => 'linear-gradient(135deg,#f3e5f5,#e1bee7)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),

            // ── CUSTOM STITCHING ──────────────────────────────────────────
            new product([
                'id'          => 'stitching-tshirt',
                'name'        => 'Custom Stitched T-Shirt',
                'category'    => 'stitching',
                'description' => 'Tailored t-shirts with custom stitching and embroidery. Perfect for teams, events, and branded apparel.',
                'price'       => 0,
                'icon'        => 'fa-cut',
                'bgGradient'  => 'linear-gradient(135deg,#e8f5e9,#c8e6c9)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),

            // ── STICKERS & DECALS ─────────────────────────────────────────
            new product([
                'id'          => 'motorcycle-decal',
                'name'        => 'Motorcycle Decals',
                'category'    => 'sticker',
                'description' => 'High-quality waterproof motorcycle decals in any shape and design. Weather-resistant and long-lasting.',
                'price'       => 0,
                'icon'        => 'fa-motorcycle',
                'bgGradient'  => 'linear-gradient(135deg,#fce4ec,#f8bbd0)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'truck-decal',
                'name'        => 'Truck Decals',
                'category'    => 'sticker',
                'description' => 'Large-format truck decals and vinyl wraps. Bold, vibrant, and built to withstand the elements.',
                'price'       => 0,
                'icon'        => 'fa-truck',
                'bgGradient'  => 'linear-gradient(135deg,#fce4ec,#f8bbd0)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),
            new product([
                'id'          => 'car-decal',
                'name'        => 'Car Decals',
                'category'    => 'sticker',
                'description' => 'Custom car decals and stickers. Perfect for business branding, personal style, or promotional use.',
                'price'       => 0,
                'icon'        => 'fa-car',
                'bgGradient'  => 'linear-gradient(135deg,#fce4ec,#f8bbd0)',
                'photo'       => '',
                'photos'      => [],
                'variants'    => [],
            ]),

        ];
    }

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
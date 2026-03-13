<?php
/**
 * product.php
 * Product Model — Single Responsibility: manages product data only.
 *
 * SOLID notes for teammates:
 * ─ Single Responsibility : this class only holds product data + factory methods
 * ─ Open/Closed           : add new products by adding to the array below — no other file changes needed
 * ─ Interface Segregation : getters are split by concern (display, category, pricing)
 *
 * HOW TO ADD A NEW PRODUCT (for admin/backend teammate):
 * 1. Copy one of the existing product blocks below
 * 2. Give it a unique 'id' (lowercase, no spaces)
 * 3. Set the correct 'category' — must match one of: sublimation | tarpaulin | uniform | mug | lanyard | stitching | sticker
 * 4. Fill in name, description, price (0 = "Contact us for pricing")
 * 5. Pick an icon from https://fontawesome.com/icons
 * 6. Save — the product will automatically appear on the Services page
 */
class product {

    // ── Product properties ────────────────────────────────────────────────
    private string $id;
    private string $name;
    private string $category;
    private string $description;
    private float  $price;
    private string $icon;
    private string $bgGradient;
    private string $photo; // Path to actual product photo (set by admin)

    public function __construct(array $data) {
        $this->id          = $data['id']          ?? '';
        $this->name        = $data['name']        ?? '';
        $this->category    = $data['category']    ?? '';
        $this->description = $data['description'] ?? '';
        $this->price       = $data['price']       ?? 0.0;
        $this->icon        = $data['icon']        ?? 'fa-image';
        $this->bgGradient  = $data['bgGradient']  ?? 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
        $this->photo       = $data['photo']       ?? ''; // Empty until admin uploads a photo
    }

    // ── Getters ───────────────────────────────────────────────────────────

    public function getId(): string          { return $this->id; }
    public function getName(): string        { return $this->name; }
    public function getCategory(): string    { return $this->category; }
    public function getDescription(): string { return $this->description; }
    public function getPrice(): float        { return $this->price; }
    public function getIcon(): string        { return $this->icon; }
    public function getBgGradient(): string  { return $this->bgGradient; }
    public function getPhoto(): string       { return $this->photo; }

    /** Returns formatted price or fallback text */
    public function getPriceFormatted(): string {
        return $this->price > 0
            ? '₱' . number_format($this->price, 2)
            : 'Contact us for pricing';
    }

    /** Returns the CSS class for the card image background */
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

    // ══════════════════════════════════════════════════════════════════════
    //  PRODUCT CATALOGUE
    //  To add a product: copy a block, change the values, save the file.
    // ══════════════════════════════════════════════════════════════════════
    public static function getAllProducts(): array {
        return [

            // ── SUBLIMATION ───────────────────────────────────────────────
            new product([
                'id'          => 'jersey',
                'name'        => 'Jersey',
                'category'    => 'sublimation',
                'description' => 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',
                'price'       => 0,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => '',
            ]),
            new product([
                'id'          => 'tshirt',
                'name'        => 'T-Shirt',
                'category'    => 'sublimation',
                'description' => 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',
                'price'       => 0,
                'icon'        => 'fa-tshirt',
                'bgGradient'  => 'linear-gradient(135deg,#fff5cc,#ffe57a)',
                'photo'       => '',
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
            ]),

            // ── SUBLIMATION MUGS ──────────────────────────────────────────
            new product([
                'id'          => 'mug',
                'name'        => 'Sublimation Mug',
                'category'    => 'mug',
                'description' => 'Full-wrap sublimation printed mugs with your custom design. Perfect for gifts, souvenirs, and corporate giveaways.',
                'price'       => 0,
                'icon'        => 'fa-mug-hot',
                'bgGradient'  => 'linear-gradient(135deg,#fff3e0,#ffe0b2)',
                'photo'       => '',
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
            ]),

        ];
    }

    // ── Helper: filter by category ────────────────────────────────────────
    public static function getByCategory(string $category): array {
        return array_values(array_filter(
            self::getAllProducts(),
            fn($p) => $p->getCategory() === $category
        ));
    }

    // ── Helper: find one product by id ────────────────────────────────────
    public static function getById(string $id): ?product {
        foreach (self::getAllProducts() as $product) {
            if ($product->getId() === $id) return $product;
        }
        return null;
    }
}
?>
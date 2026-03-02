<?php
/**
 * Product Model
 * Single Responsibility: Manages product data
 */
class Product {
    private string $id;
    private string $name;
    private string $category;
    private string $description;
    private float $price;
    private string $icon;
    private string $bgGradient;

    public function __construct(array $data) {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->price = $data['price'] ?? 0.0;
        $this->icon = $data['icon'] ?? 'fa-image';
        $this->bgGradient = $data['bgGradient'] ?? 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
    }

    public function getId(): string {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getPriceFormatted(): string {
        return $this->price > 0 ? '₱' . number_format($this->price, 2) : 'Contact us for pricing';
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function getBgGradient(): string {
        return $this->bgGradient;
    }

    public function getCategoryClass(): string {
        return $this->category === 'sublimation' ? 'sublim-img' : 'tarp-img';
    }

    /**
     * Get all products (Static Factory Method)
     */
    public static function getAllProducts(): array {
        return [
            // Sublimation Products
            new Product([
                'id' => 'jersey',
                'name' => 'Jersey',
                'category' => 'sublimation',
                'description' => 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',
                'price' => 0,
                'icon' => 'fa-tshirt',
                'bgGradient' => 'linear-gradient(135deg,#fff5cc,#ffe57a)'
            ]),
            new Product([
                'id' => 'tshirt',
                'name' => 'T-Shirt',
                'category' => 'sublimation',
                'description' => 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',
                'price' => 0,
                'icon' => 'fa-tshirt',
                'bgGradient' => 'linear-gradient(135deg,#fff5cc,#ffe57a)'
            ]),
            new Product([
                'id' => 'short',
                'name' => 'Short',
                'category' => 'sublimation',
                'description' => 'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.',
                'price' => 0,
                'icon' => 'fa-tshirt',
                'bgGradient' => 'linear-gradient(135deg,#fff5cc,#ffe57a)'
            ]),
            new Product([
                'id' => 'warmer',
                'name' => 'Warmer',
                'category' => 'sublimation',
                'description' => 'Sublimation warmers for players and athletes. Keeps you warm while looking professional on and off the court.',
                'price' => 0,
                'icon' => 'fa-tshirt',
                'bgGradient' => 'linear-gradient(135deg,#fff5cc,#ffe57a)'
            ]),
            new Product([
                'id' => 'joggingpants',
                'name' => 'Jogging Pants',
                'category' => 'sublimation',
                'description' => 'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching for any team or individual.',
                'price' => 0,
                'icon' => 'fa-tshirt',
                'bgGradient' => 'linear-gradient(135deg,#fff5cc,#ffe57a)'
            ]),
            
            // Tarpaulin Products
            new Product([
                'id' => 'birthday',
                'name' => 'Birthday Tarpaulin',
                'category' => 'tarpaulin',
                'description' => 'Beautiful custom birthday tarpaulins made to celebrate your special day. Any size, any design — bold and colorful.',
                'price' => 0,
                'icon' => 'fa-birthday-cake',
                'bgGradient' => 'linear-gradient(135deg,#ffe0e0,#ffb3b3)'
            ]),
            new Product([
                'id' => 'graduation',
                'name' => 'Graduation Tarpaulin',
                'category' => 'tarpaulin',
                'description' => 'Celebrate achievements with stunning graduation tarpaulins. Custom designs that make the moment unforgettable.',
                'price' => 0,
                'icon' => 'fa-graduation-cap',
                'bgGradient' => 'linear-gradient(135deg,#ffe0e0,#ffb3b3)'
            ]),
            new Product([
                'id' => 'congratulation',
                'name' => 'Congratulation Tarpaulin',
                'category' => 'tarpaulin',
                'description' => 'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.',
                'price' => 0,
                'icon' => 'fa-star',
                'bgGradient' => 'linear-gradient(135deg,#ffe0e0,#ffb3b3)'
            ])
        ];
    }

    /**
     * Get products by category
     */
    public static function getByCategory(string $category): array {
        return array_filter(self::getAllProducts(), function($product) use ($category) {
            return $product->getCategory() === $category;
        });
    }

    /**
     * Get product by ID
     */
    public static function getById(string $id): ?Product {
        foreach (self::getAllProducts() as $product) {
            if ($product->getId() === $id) {
                return $product;
            }
        }
        return null;
    }
}
?>
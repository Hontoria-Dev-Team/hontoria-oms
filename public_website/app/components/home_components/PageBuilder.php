<?php
/**
 * Page Builder
 * Open/Closed Principle: Open for extension, closed for modification
 * Assembles components into a complete page
 */
class PageBuilder {
    private array $components = [];
    private array $meta = [];
    private array $assets = [];

    public function __construct() {
        $this->meta = [
            'title' => 'Hontoria Printing Services',
            'charset' => 'UTF-8',
            'viewport' => 'width=device-width, initial-scale=1.0'
        ];
    }

    /**
     * Add component to page (Dependency Inversion: depends on Component abstraction)
     */
    public function addComponent(\Component $component): self {
        $this->components[] = $component;
        return $this;
    }

    /**
     * Set page metadata
     */
    public function setMeta(array $meta): self {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    /**
     * Add CSS file
     */
    public function addCSS(string $path): self {
        $this->assets['css'][] = $path;
        return $this;
    }

    /**
     * Add JS file
     */
    public function addJS(string $path): self {
        $this->assets['js'][] = $path;
        return $this;
    }

    /**
     * Render complete HTML page
     */
    public function build(): string {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="<?php echo htmlspecialchars($this->meta['charset']); ?>"/>
            <meta name="viewport" content="<?php echo htmlspecialchars($this->meta['viewport']); ?>"/>
            <title><?php echo htmlspecialchars($this->meta['title']); ?></title>
            <link rel="icon" type="image/jpeg" href="img/logo.jpg"/>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
            <?php foreach ($this->assets['css'] ?? [] as $css): ?>
                <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>"/>
            <?php endforeach; ?>
        </head>
        <body>
            <?php foreach ($this->components as $component): ?>
                <?php echo $component->render(); ?>
            <?php endforeach; ?>
            
            <?php foreach ($this->assets['js'] ?? [] as $js): ?>
                <script src="<?php echo htmlspecialchars($js); ?>"></script>
            <?php endforeach; ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
?>
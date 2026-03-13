<?php
/**
 * Configuration Class
 * Single Responsibility: Manages all site configuration
 */
class Config {
    private static ?Config $instance = null;
    private array $config;

    private function __construct() {
        // Get the base URL dynamically
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']); // Gets /HONTORIA-OMS/public_website/public
        
        // Calculate path to Shared folder (go up from public_website/public to HONTORIA-OMS, then to Shared)
        $basePath = dirname(dirname($scriptPath)); // Goes to /HONTORIA-OMS
        
        $this->config = [
            'site' => [
                'name' => 'HONTORIA PRINTING SERVICES',
                'tagline' => 'Quality. Affordable. Fast.',
                'fbLink' => 'https://www.facebook.com/jhong.hontoria.3',
                'address' => 'Feeder Road 2, Brgy. Tibal-og Santo tomas, Davao del Norte',
                'logoPath' => $basePath . '/Shared/img/Logo.png'
            ],
            'navigation' => [
                ['label' => 'HOME',     'url' => '?page=home',     'active' => true],
                ['label' => 'SERVICES', 'url' => '?page=services', 'active' => false],
                ['label' => 'ABOUT US', 'url' => '?page=about',    'active' => false]
            ],
            'paths' => [
                'css' => $scriptPath . '/css/home.css',
                'js' => $scriptPath . '/js/home.js',
                'base_url' => $scriptPath
            ]
        ];
    }

    /**
     * Singleton pattern for global config access
     */
    public static function getInstance(): Config {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    /**
     * Get configuration value
     */
    public function get(string $key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set configuration value
     */
    public function set(string $key, $value): void {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Get all config
     */
    public function all(): array {
        return $this->config;
    }
}
?>
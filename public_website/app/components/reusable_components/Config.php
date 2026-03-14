<?php
/**
 * Config.php
 * Single Responsibility: Manages all site configuration.
 * Location: app/components/reusable_components/Config.php
 */
class Config {
    private static ?Config $instance = null;
    private array $config;

    private function __construct() {
        $protocol   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host       = $_SERVER['HTTP_HOST'];
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']); // /HONTORIA-OMS/public_website/public

        // Full base URL: http://localhost/HONTORIA-OMS/public_website/public
        $baseUrl  = $protocol . '://' . $host . $scriptPath;

        // Path to Shared folder (2 levels up from public)
        $basePath = dirname(dirname($scriptPath)); // /HONTORIA-OMS

        $this->config = [
            'site' => [
                'name'     => 'HONTORIA PRINTING SERVICES',
                'tagline'  => 'Quality. Affordable. Fast.',
                'fbLink'   => 'https://www.facebook.com/jhong.hontoria.3',
                'address'  => 'Feeder Road 2, Brgy. Tibal-og Santo tomas, Davao del Norte',
                'logoPath' => $basePath . '/Shared/img/Logo.png',
            ],
            'navigation' => [
                ['label' => 'HOME',     'url' => '?page=home',     'active' => true],
                ['label' => 'SERVICES', 'url' => '?page=services', 'active' => false],
                ['label' => 'ABOUT US', 'url' => '?page=about',    'active' => false],
            ],
            'paths' => [
                // Full URLs used directly in <link href=""> and <script src="">
                'base_url' => $baseUrl,
                'css'      => $baseUrl . '/css/home.css',
                'js'       => $baseUrl . '/js/home.js',
            ],
        ];
    }

    public static function getInstance(): Config {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function get(string $key, $default = null) {
        $keys  = explode('.', $key);
        $value = $this->config;
        foreach ($keys as $k) {
            if (!isset($value[$k])) return $default;
            $value = $value[$k];
        }
        return $value;
    }

    public function set(string $key, $value): void {
        $keys   = explode('.', $key);
        $config = &$this->config;
        foreach ($keys as $k) {
            if (!isset($config[$k])) $config[$k] = [];
            $config = &$config[$k];
        }
        $config = $value;
    }

    public function all(): array {
        return $this->config;
    }
}
?>
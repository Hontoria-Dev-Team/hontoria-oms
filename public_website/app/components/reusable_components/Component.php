<?php
/**
 * Base Component Class
 * Single Responsibility: Provides base rendering functionality for all components
 */
abstract class Component {
    protected array $data = [];
    protected array $assets = [];

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * Render the component HTML
     * @return string
     */
    abstract public function render(): string;

    /**
     * Escape HTML output for security
     */
    protected function escape(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get data value with default fallback
     */
    protected function get(string $key, $default = '') {
        return $this->data[$key] ?? $default;
    }
}
?>
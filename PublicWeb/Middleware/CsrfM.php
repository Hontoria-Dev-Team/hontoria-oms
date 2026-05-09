<?php
class CsrfM {
    private const CSRF_TOKEN_SESSION_KEY = '_csrf_token';
    private const CSRF_TOKEN_LENGTH = 32;

    /**
     * Initialize CSRF token in session if not already present
     */
    public static function initializeToken(): void {
        if (!isset($_SESSION[self::CSRF_TOKEN_SESSION_KEY])) {
            $_SESSION[self::CSRF_TOKEN_SESSION_KEY] = self::generateToken();
        }
    }

    /**
     * Generate a new CSRF token
     */
    private static function generateToken(): string {
        return bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
    }

    /**
     * Retrieve the current CSRF token from session
     */
    public static function getToken(): string {
        self::initializeToken();
        return $_SESSION[self::CSRF_TOKEN_SESSION_KEY];
    }

    /**
     * Validate CSRF token from POST/REQUEST data
     * Throws exception if token is invalid or missing
     */
    public static function validateToken(): bool {
        self::initializeToken();

        // Get token from POST data
        $tokenFromRequest = $_POST['_csrf_token'] ?? null;

        if ($tokenFromRequest === null) {
            throw new Exception("CSRF token is missing from request.");
        }

        $sessionToken = $_SESSION[self::CSRF_TOKEN_SESSION_KEY];

        // Use hash_equals to prevent timing attacks
        if (!hash_equals($sessionToken, $tokenFromRequest)) {
            throw new Exception("CSRF token validation failed. Request rejected.");
        }

        return true;
    }

    /**
     * Regenerate a new CSRF token (useful after sensitive operations)
     */
    public static function regenerateToken(): void {
        $_SESSION[self::CSRF_TOKEN_SESSION_KEY] = self::generateToken();
    }

    /**
     * Get HTML hidden input field for CSRF token
     */
    public static function getTokenField(): string {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

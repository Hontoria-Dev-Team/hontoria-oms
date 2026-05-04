<?php
class SecurityHeadersMid {
    public static function setHeaders(): void {
        header("X-Frame-Options: SAMEORIGIN");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self';");

        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        } else {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
}

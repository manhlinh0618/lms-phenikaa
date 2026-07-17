<?php
class Security {
    // Escape output chống XSS
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // Set các Security Headers cơ bản
    public static function setSecurityHeaders() {
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        header("Content-Security-Policy: default-src 'self'");
    }

    // Cấu hình cookie an toàn cho session
    public static function configureSecureSessions() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1); // Cần HTTPS
        ini_set('session.cookie_samesite', 'Strict');
    }
}
?>

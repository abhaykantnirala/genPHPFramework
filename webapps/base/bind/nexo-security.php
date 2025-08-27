<?php
/**
 * Nexo Framework Security and Input Validation System
 * 
 * Provides comprehensive input validation, sanitization, and security features
 * Protects against XSS, SQL injection, path traversal, and other attacks
 * 
 * @author    Nexo Framework Security Team
 * @copyright 2024 Nexo Framework
 * @license   MIT License
 * @version   1.0.0
 */

class NexoSecurity {
    
    // Input validation patterns
    private static $patterns = [
        'alphanumeric' => '/^[a-zA-Z0-9]+$/',
        'alpha' => '/^[a-zA-Z]+$/',
        'numeric' => '/^[0-9]+$/',
        'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'url' => '/^https?:\/\/[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}[\/\w.-]*\/?$/',
        'slug' => '/^[a-z0-9-]+$/',
        'filename' => '/^[a-zA-Z0-9._-]+$/',
        'path' => '/^[a-zA-Z0-9\/_.-]+$/',
    ];
    
    // Dangerous characters and patterns
    private static $xss_patterns = [
        '/<script[^>]*>.*?<\/script>/is',
        '/<iframe[^>]*>.*?<\/iframe>/is',
        '/<object[^>]*>.*?<\/object>/is',
        '/<embed[^>]*>.*?<\/embed>/is',
        '/<applet[^>]*>.*?<\/applet>/is',
        '/<form[^>]*>.*?<\/form>/is',
        '/javascript:/i',
        '/vbscript:/i',
        '/onload=/i',
        '/onerror=/i',
        '/onclick=/i',
        '/onmouseover=/i',
    ];
    
    /**
     * Sanitize input string for safe usage
     * 
     * @param mixed $input Input to sanitize
     * @param string $type Type of sanitization
     * @return mixed Sanitized input
     */
    public static function sanitize($input, string $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitize($item, $type);
            }, $input);
        }
        
        if (!is_string($input)) {
            return $input;
        }
        
        switch ($type) {
            case 'string':
                return self::sanitizeString($input);
            case 'html':
                return self::sanitizeHtml($input);
            case 'url':
                return self::sanitizeUrl($input);
            case 'filename':
                return self::sanitizeFilename($input);
            case 'path':
                return self::sanitizePath($input);
            case 'sql':
                return self::sanitizeSql($input);
            case 'int':
                return self::sanitizeInt($input);
            case 'float':
                return self::sanitizeFloat($input);
            case 'email':
                return self::sanitizeEmail($input);
            default:
                return self::sanitizeString($input);
        }
    }
    
    /**
     * Validate input against specified rules
     * 
     * @param mixed $input Input to validate
     * @param string $rule Validation rule
     * @param array $options Additional validation options
     * @return bool True if valid, false otherwise
     */
    public static function validate($input, string $rule, array $options = []): bool {
        if (is_array($input)) {
            foreach ($input as $item) {
                if (!self::validate($item, $rule, $options)) {
                    return false;
                }
            }
            return true;
        }
        
        switch ($rule) {
            case 'required':
                return !empty(trim($input));
            case 'length':
                $min = $options['min'] ?? 0;
                $max = $options['max'] ?? PHP_INT_MAX;
                $len = strlen($input);
                return $len >= $min && $len <= $max;
            case 'pattern':
                if (isset($options['pattern'])) {
                    return preg_match($options['pattern'], $input) === 1;
                }
                return false;
            case 'in':
                return isset($options['values']) && in_array($input, $options['values']);
            case 'not_in':
                return !isset($options['values']) || !in_array($input, $options['values']);
            default:
                if (isset(self::$patterns[$rule])) {
                    return preg_match(self::$patterns[$rule], $input) === 1;
                }
                return false;
        }
    }
    
    /**
     * Sanitize string input
     */
    private static function sanitizeString(string $input): string {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove XSS patterns
        foreach (self::$xss_patterns as $pattern) {
            $input = preg_replace($pattern, '', $input);
        }
        
        // HTML encode special characters
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }
    
    /**
     * Sanitize HTML input (allows safe HTML tags)
     */
    private static function sanitizeHtml(string $input): string {
        // Allow only safe HTML tags
        $allowed_tags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a>';
        $input = strip_tags($input, $allowed_tags);
        
        // Remove dangerous attributes
        $input = preg_replace('/(<[^>]*)\s+(on\w+|javascript:|vbscript:)[^>]*>/i', '$1>', $input);
        
        return $input;
    }
    
    /**
     * Sanitize URL input
     */
    private static function sanitizeUrl(string $input): string {
        // Remove dangerous protocols
        $input = preg_replace('/^(javascript|vbscript|data|file):/i', '', $input);
        
        // Validate and sanitize URL
        $input = filter_var($input, FILTER_SANITIZE_URL);
        
        return $input;
    }
    
    /**
     * Sanitize filename
     */
    private static function sanitizeFilename(string $input): string {
        // Remove path traversal attempts
        $input = str_replace(['../', '..\\', '../\\'], '', $input);
        
        // Remove dangerous characters
        $input = preg_replace('/[^a-zA-Z0-9._-]/', '', $input);
        
        // Limit length
        $input = substr($input, 0, 255);
        
        return $input;
    }
    
    /**
     * Sanitize file path
     */
    private static function sanitizePath(string $input): string {
        // Remove path traversal attempts
        $input = str_replace(['../', '..\\', '../\\'], '', $input);
        
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove dangerous characters
        $input = preg_replace('/[^a-zA-Z0-9\/_.-]/', '', $input);
        
        // Normalize path separators
        $input = str_replace('\\', '/', $input);
        
        // Remove multiple consecutive slashes
        $input = preg_replace('/\/+/', '/', $input);
        
        return $input;
    }
    
    /**
     * Sanitize for SQL (basic - use prepared statements instead)
     */
    private static function sanitizeSql(string $input): string {
        // This is basic sanitization - ALWAYS use prepared statements for SQL
        $input = str_replace(["'", '"', ';', '--', '/*', '*/'], '', $input);
        return addslashes($input);
    }
    
    /**
     * Sanitize integer input
     */
    private static function sanitizeInt($input): int {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitize float input
     */
    private static function sanitizeFloat($input): float {
        return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Sanitize email input
     */
    private static function sanitizeEmail(string $input): string {
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Detect potential XSS attacks
     * 
     * @param string $input Input to check
     * @return bool True if XSS detected
     */
    public static function detectXSS(string $input): bool {
        foreach (self::$xss_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Detect SQL injection attempts
     * 
     * @param string $input Input to check
     * @return bool True if SQL injection detected
     */
    public static function detectSQLInjection(string $input): bool {
        $sql_patterns = [
            '/(\bunion\s+select)/i',
            '/(\bselect\s+.*\bfrom)/i',
            '/(\binsert\s+into)/i',
            '/(\bupdate\s+.*\bset)/i',
            '/(\bdelete\s+from)/i',
            '/(\bdrop\s+table)/i',
            '/(\bdrop\s+database)/i',
            '/(\balter\s+table)/i',
            '/(\bexec\s*\()/i',
            '/(\bexecute\s*\()/i',
        ];
        
        foreach ($sql_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Detect path traversal attempts
     * 
     * @param string $input Input to check
     * @return bool True if path traversal detected
     */
    public static function detectPathTraversal(string $input): bool {
        $traversal_patterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/',
            '/%2e%2e%5c/',
            '/\0/',
        ];
        
        foreach ($traversal_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Log security violation
     * 
     * @param string $type Type of violation
     * @param string $input Malicious input
     * @param string $ip Client IP address
     */
    public static function logSecurityViolation(string $type, string $input, string $ip = ''): void {
        if (empty($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $logMessage = sprintf(
            "[%s] SECURITY VIOLATION: %s | IP: %s | Input: %s\n",
            date('Y-m-d H:i:s'),
            $type,
            $ip,
            substr($input, 0, 200) // Limit log size
        );
        
        // Log to security log if enabled
        if (defined('NEXO_LOG_ERRORS') && NEXO_LOG_ERRORS === true) {
            $logFile = defined('NEXO_LOG_FILE') ? 
                str_replace('nexo_errors.log', 'nexo_security.log', NEXO_LOG_FILE) : 
                APPPATH . 'logs/nexo_security.log';
            error_log($logMessage, 3, $logFile);
        }
        
        // Also log to system error log
        error_log("Nexo Security: $type violation from $ip");
    }
    
    /**
     * Comprehensive input validation for Nexo Framework
     * 
     * @param mixed $input Input to validate and sanitize
     * @param array $rules Validation rules
     * @return array [sanitized_input, is_valid, errors]
     */
    public static function validateInput($input, array $rules = []): array {
        $sanitized = $input;
        $is_valid = true;
        $errors = [];
        
        // Default sanitization
        $sanitized = self::sanitize($input, $rules['type'] ?? 'string');
        
        // Security checks
        if (is_string($input)) {
            if (self::detectXSS($input)) {
                $is_valid = false;
                $errors[] = 'XSS attempt detected';
                self::logSecurityViolation('XSS', $input);
            }
            
            if (self::detectSQLInjection($input)) {
                $is_valid = false;
                $errors[] = 'SQL injection attempt detected';
                self::logSecurityViolation('SQL_INJECTION', $input);
            }
            
            if (self::detectPathTraversal($input)) {
                $is_valid = false;
                $errors[] = 'Path traversal attempt detected';
                self::logSecurityViolation('PATH_TRAVERSAL', $input);
            }
        }
        
        // Apply validation rules
        foreach ($rules as $rule => $options) {
            if ($rule === 'type') continue; // Already handled
            
            if (!self::validate($sanitized, $rule, is_array($options) ? $options : [])) {
                $is_valid = false;
                $errors[] = "Validation failed for rule: $rule";
            }
        }
        
        return [$sanitized, $is_valid, $errors];
    }
}
?>

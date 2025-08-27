<?php
/**
 * Nexo Framework - Exception Classes
 * 
 * Custom exception handling for better error management
 * 
 * @author    Nexo Framework Development Team
 * @copyright 2024 Nexo Framework
 * @license   MIT License
 * @version   1.0.0
 */

/**
 * Base Nexo Exception Class
 */
class NexoException extends Exception {
    
    protected $context = [];
    
    public function __construct($message = "", $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    
    /**
     * Get additional context information
     */
    public function getContext() {
        return $this->context;
    }
    
    /**
     * Set additional context information
     */
    public function setContext(array $context) {
        $this->context = $context;
        return $this;
    }
}

/**
 * Controller Related Exceptions
 */
class NexoControllerException extends NexoException {
    
}

/**
 * Controller File Not Found Exception
 */
class ControllerNotFoundException extends NexoControllerException {
    
    public function __construct($controllerPath, $code = 404, Exception $previous = null) {
        $message = "Controller file not found: '{$controllerPath}'";
        $context = [
            'controller_path' => $controllerPath,
            'type' => 'controller_not_found'
        ];
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Controller Method Not Found Exception
 */
class ControllerMethodNotFoundException extends NexoControllerException {
    
    public function __construct($controller, $availableMethods = [], $code = 404, Exception $previous = null) {
        $message = "No valid methods found in controller: '{$controller}'";
        $context = [
            'controller' => $controller,
            'available_methods' => $availableMethods,
            'type' => 'controller_method_not_found'
        ];
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Routing Related Exceptions
 */
class NexoRoutingException extends NexoException {
    
}

/**
 * Route Not Found Exception
 */
class RouteNotFoundException extends NexoRoutingException {
    
    public function __construct($uri, $availableRoutes = [], $code = 404, Exception $previous = null) {
        $message = "No route found matching URI: '{$uri}'";
        $context = [
            'uri' => $uri,
            'available_routes' => $availableRoutes,
            'type' => 'route_not_found'
        ];
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * File System Related Exceptions
 */
class NexoFileSystemException extends NexoException {
    
}

/**
 * Directory Not Found Exception
 */
class DirectoryNotFoundException extends NexoFileSystemException {
    
    public function __construct($directory, $code = 500, Exception $previous = null) {
        $message = "Directory not found: '{$directory}'";
        $context = [
            'directory' => $directory,
            'type' => 'directory_not_found'
        ];
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Middleware Related Exceptions
 */
class NexoMiddlewareException extends NexoException {
    
}

/**
 * Middleware File Not Found Exception
 */
class MiddlewareNotFoundException extends NexoMiddlewareException {
    
    public function __construct($middlewarePath, $code = 500, Exception $previous = null) {
        $message = "Middleware file not found: '{$middlewarePath}'";
        $context = [
            'middleware_path' => $middlewarePath,
            'type' => 'middleware_not_found'
        ];
        parent::__construct($message, $code, $previous, $context);
    }
}

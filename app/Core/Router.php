<?php

namespace App\Core;

/**
 * Classe Router - Gestion du routage des URLs
 */
class Router
{
    private array $routes = [];
    private string $basePath;
    
    /**
     * Constructeur
     * @param string $basePath Chemin de base de l'application
     */
    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
    }
    
    /**
     * Ajouter une route GET
     * @param string $path Chemin de la route
     * @param callable|array $handler Gestionnaire (fonction ou [Controller, method])
     * @return void
     */
    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Ajouter une route POST
     * @param string $path Chemin de la route
     * @param callable|array $handler Gestionnaire (fonction ou [Controller, method])
     * @return void
     */
    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Ajouter une route
     * @param string $method Méthode HTTP
     * @param string $path Chemin de la route
     * @param callable|array $handler Gestionnaire
     * @return void
     */
    private function addRoute(string $method, string $path, $handler): void
    {
        // Conversion du chemin en regex pour supporter les paramètres dynamiques
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    /**
     * Dispatcher la requête
     * @return void
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Supprimer la query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Supprimer le basePath
        if (!empty($this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        // S'assurer que l'URI commence par /
        $uri = '/' . ltrim($uri, '/');
        
        // Rechercher une route correspondante
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Extraire les paramètres
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Appeler le handler
                $this->callHandler($route['handler'], $params);
                return;
            }
        }
        
        // Aucune route trouvée - 404
        $this->notFound();
    }
    
    /**
     * Appeler le handler d'une route
     * @param callable|array $handler Gestionnaire
     * @param array $params Paramètres extraits de l'URL
     * @return void
     */
    private function callHandler($handler, array $params): void
    {
        if (is_array($handler)) {
            // Format: [ControllerClass, 'method']
            [$controllerClass, $method] = $handler;
            
            // Créer une instance du contrôleur
            $controller = new $controllerClass();
            
            // Appeler la méthode avec les paramètres
            call_user_func_array([$controller, $method], $params);
        } elseif (is_callable($handler)) {
            // Fonction anonyme
            call_user_func_array($handler, $params);
        }
    }
    
    /**
     * Page 404
     * @return void
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1>";
        exit;
    }
}


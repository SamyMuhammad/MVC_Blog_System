<?php

namespace System;

class Route {
    /**
     * Application object 
     * @var \System\Application 
     */
    private $app;
    /**
     * Routes container
     * @var array
     */
    private $routes = [];
    /**
     * Not found URL
     * @var string
     */
    private $notFound;
    
    /**
     * Constructor
     * @param \System\Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * Add new route
     * @param string $url
     * @param string $action
     * @param string $requestMethod
     */
    public function add($url, $action, $requestMethod = 'GET') {
        $route = [
            'url'     => $url,
            'pattern' => $this->generatePattern($url),
            'action'  => $this->getAction($action),
            'method'  => strtoupper($requestMethod)
        ];
        $this->routes[] = $route;
    }
    /**
     *Set not found URL
     * @param string $url
     * @return void 
     */
    public function notFound($url) {
        $this->notFound = $url;
    }
    /**
     * Get proper route
     * @return array
     */
    public function getProperRoute() {
        //pre($this->routes);
        foreach( $this->routes as $route){
            if ($this->isMatching($route['pattern'])){
                $arguments = $this->getArgumentsFrom($route['pattern']);
                //controller@method
                list($controller, $method) = explode('@', $route['action']);
                return [$controller, $method, $arguments];
            }
        }
    }
    /**
     * Determine if the given pattern matches the current request URL
     * @param string $pattern
     * @return bool
     */
    private function isMatching($pattern) {
        return preg_match($pattern, $this->app->request->url());
    }
    /**
     * Get arguments from the requested URL based on the given pattern
     * @param string $pattern
     * @return array
     */
    private function getArgumentsFrom($pattern){
        preg_match($pattern, $this->app->request->url(), $matches);
        //pre ($matches);
        array_shift($matches); // Remove the first element of the array.
        return $matches;
    }
    /**
     * Generate a regex pattern for the given URL
     * @param string $url
     * @return string
     */
    private function generatePattern($url) {
        $pattern = '#^';
        $pattern .= str_replace([':text', ':id'], ['([a-zA-Z0-9-]+)', '(\d+)'], $url);
        $pattern .= '$#';
        return $pattern;
    }
    /**
     * Get the proper action 
     * @param string $action
     * @return string
     */
    private function getAction($action) {
        $action = str_replace('/', '\\', $action);
        return strpos($action, '@') !== false ? $action : $action . '@index';
    }
}

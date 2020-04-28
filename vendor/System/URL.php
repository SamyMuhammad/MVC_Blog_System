<?php

namespace System;

/**
 * URL class generates the full URL for any link 
 *
 */
class URL {
    /**
     * Application object
     * @var \System\Application
    */
    protected $app;
    
    /*
     * Constructor
     * @param \System\Application $app
     * @return object
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * Generate the full link for the given path(URL)
     * @param string $path
     * @return string
     */
    public function link($path) {
        return $this->app->request->baseURL() . trim($path, '/');
    }
    /**
     * Redirect to the given path
     * @param string $path
     * @return voud
     */
    public function redirectTo($path) {
        header('location:' . $this->link($path));
        exit();
    }
}

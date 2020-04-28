<?php

namespace System\View;

use System\Application;

class ViewFactory {
    /**
     * Application object
     * @var System\Application $app
     */
    private $app;
    
    /**
     * Constructor
     * @param System\Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * Render the given view path with the passed variables and generate new view object
     * @param string $viewPath
     * @param array $data
     * @return System\View\ViewInterface
     */
    public function render($viewPath, array $data = []) {
        return new View($this->app->file, $viewPath, $data);
    }
    
}

<?php

namespace System;

class Application {
    /**
     *Container
     * @var array 
     */
    private $container=[];
    /**
     * Application object
     * @var \System\Application
     */
    private static $instance;

    /**
     * Constructor
     * @param System\File $file
     */
    private function __construct(File $file) {
        $this->share('file', $file);
        $this->registerClasses();
        $this->loadHelpers();
    }
    /**
     * Get application instance
     * @param System\File $file 
     * @return \System\Application 
     */
    public static function getInstance($file = null) {
        if (is_null(static::$instance)){
            static::$instance = new static($file);  //static, in PHP 5.3's late static bindings, refers to whatever class in the hierarchy you called the method on.
        }
        return static::$instance;
    }
    /**
     * Run the application
     * @return void
     */
    public function run() {
        $this->session->start();
        $this->request->prepareUrl(); //Analyze the url and store its components in Request properties.
        $this->file->call('App/index.php');
        list($controller, $method, $arguments) = $this->route->getProperRoute();
        $this->load->controller($controller);
        $output = (string) $this->load->action($controller, $method, $arguments);
        $this->response->setOutput($output);
        $this->response->send();
    }
    /**
     * Register classes in spl_auto_load register
     * 'spl_autoload_register()' called when -- and only when -- PHP needs to access a class that has not been defined.
     * @return void
     */
    private function registerClasses() {
        spl_autoload_register([$this, 'load']); //this function passes the full class name including namespaces
    }
    /**
     * Load classes
     * @param string $class
     * @return void
     */
    public function load($class) {
        if (strpos($class, 'App') === 0) { //The class is in App folder
            $file = $class . '.php';
        }else{
            //Get class from vendor
            $file = 'vendor/' . $class . '.php';
        }
        if ($this->file->exists($file)){
                $this->file->call($file);
            }
    }
    /**
     * Load helpers file
     * @return void
     */
    private function loadHelpers() {
        $this->file->call('vendor/helpers.php');
    }
    /**
     * Get shared value
     * @param string $key
     * @return mixed
     */
    public function get($key){
        if(! $this->isSharing($key)){
            if($this->isCoreAlias($key)){
                $this->share($key, $this->createNewCoreObject($key));
            } else {
                die('<b>' . $key . '</b> not found in application container.');
            }
        }
        return $this->container[$key];


    }
    /**
     * Determine if the given key is shared through through Application
     * @param string $key
     * @return bool
     */
    public function isSharing($key) {
        return isset($this->container[$key]);
    }
    /**
     * Share the given key/value through Application 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function share($key, $value){
        $this->container[$key] = $value;
    }
    /**
     * Determine if the given key is an alias to a core class.
     * @return bool
     */
    private function isCoreAlias($alias) {
        $coreClasses = $this->coreClasses();
        return isset($coreClasses[$alias]);
    }
    /**
     * Create new object for the core class based on the given alias
     * @param string $alias
     * return object
     */
    private function createNewCoreObject($alias) {
        $coreClasses = $this->coreClasses();
        $className = $coreClasses[$alias];
        return new $className($this);
    }
    /**
     * Get all core classes with its aliases
     * @return array
     */
    private function coreClasses() {
        return array(
            'request'       => 'System\\Http\\Request',
            'response'      => 'System\\Http\\Response',
            'session'       => 'System\\Session',
            'route'         => 'System\\Route',
            'cookie'        => 'System\\Cookie',
            'load'          => 'System\\Loader',
            'html'          => 'System\\HTML',
            'db'            => 'System\\Database',
            'view'          => 'System\\View\\ViewFactory',
            'url'           => 'System\\URL'
        );
    }
    /**
     * Get shared value dynamically
     * @param string $key
     * @return mixed
     */
    public function __get($key){
        return $this->get($key);
    }
}

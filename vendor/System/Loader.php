<?php

namespace System;

class Loader {
    
    /**
     * Application object
     * @var \System\Application
     */
    private $app;
    
    /*
     * Controllers container
     * @var array
     */
    private $controllers = [];
    
    /**
     * Models container
     * @var array
     */
    private $models = [];
    
    /*
     * Constructor
     * @param \System\Application $app
     * @return object
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    
    /**
     * Call the controller and the method and pass the arguments to the method
     * @param string $controller
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function action($controller, $method, array $arguments) {
        $object = $this->controller($controller);
        return call_user_func([$object, $method], $arguments);
    }
    
    /**
     * Call the wanted controller
     * @param string $controller
     * @return object
     */
    public function controller($controller) {
        $controller = $this->getControllerName($controller);
        if (!$this->hasController($controller)){
            $this->addController($controller);
        }
        return $this->getController($controller);
    }
    
    /**
     * Determine if the controller class exists in the controllers container
     * @param string $controller
     * @return bool
     */
    private function hasController($controller) {
        return array_key_exists($controller, $this->controllers);
    }
    
    /**
     * Instantiate the controller and add the new instance to the controllers container
     * @param string $controller
     * @return void
     */
    private function addController($controller) {
        //      App\Controllers\HomeController
        $object = new $controller($this->app);
        $this->controllers[$controller] = $object;
    }
    
    /**
     * Get the controller object
     * @param string $controller
     * @return object
     */
    private function getController($controller) {
        return $this->controllers[$controller];
    }
    
    /**
     * Get the full class name for the wanted controller
     * @param string $controller
     * @return string
     */
    private function getControllerName($controller){
        $controller .= 'Controller';
        $controller = 'App\\Controllers\\' . $controller;
        return str_replace('/', '\\', $controller);
    }
    
    /**
     * Call the wanted model
     * @param string $model
     * @return object
     */
    public function model($model) {
        $model = $this->getModelName($model);
        if (!$this->hasModel($model)){
            $this->addModel($model);
        }
        return $this->getModel($model);
    }
    
    /**
     * Determine if the model class exists in the models container
     * @param string $model
     * @return bool
     */
    private function hasModel($model) {
        return array_key_exists($model, $this->models);
    }
    
    /**
     * Instantiate the model and add the new instance to the models container
     * @param string $model
     * @return void
     */
    private function addModel($model) {
        //      App\Models\HomeModel
        $object = new $model($this->app);
        $this->models[$model] = $object;
    }
    
    /**
     * Get the model object
     * @param string $model
     * @return object
     */
    private function getModel($model) {
        return $this->models[$model];
    }
    
    /**
     * Get the full class name for the wanted model
     * @param string $model
     * @return string
     */
    private function getModelName($model){
        $model .= 'Model';
        $model = 'App\\Models\\' . $model;
        return str_replace('/', '\\', $model);
    }
}

<?php

namespace System\View;

use System\File;

class View implements ViewInterface {
    /**
     * File object
     * @var System\File
     */
    private $file;
    
    /**
     * View path
     * @var string
     */
    private $viewPath;
    
    /**
     *Passed data (variables) to the View Path
     * @var array 
     */
    private $data=[];
    
    /**
     * The output from the view file
     * @var string
     */
    private $output;
    
    /**
     * Constructor
     * @param System\File $file
     * @param string $viewPath
     * @param array $data
     */
    public function __construct(File $file, $viewPath, $data) {
        $this->file = $file;
        $this->preparePath($viewPath);
        $this->data = $data;
    }
    /**
     * Prepare view path
     * @param string $viewPath
     * @return void
     */
    public function preparePath($viewPath) {
        $relativeViewPath = 'App/Views/' . $viewPath . '.php';
        $this->viewPath = $this->file->to($relativeViewPath);
        if (!$this->viewFileExists($relativeViewPath)){
            die('<b>' . $viewPath . ' View </b>' . " doesn't exist in the Views folder");
        }
    }
    /**
     * Determine if the view file exists
     * @return bool
     */
    private function viewFileExists($viewPath) {
        return $this->file->exists($viewPath);
    }
    /**
     * {@inheritDoc}
     */
    public function getOutput() {
        if (is_null($this->output)){
            ob_start();
            extract($this->data);
            require $this->viewPath;
            $this->output = ob_get_clean();            
        }
        return $this->output;
    }
    /**
     * {@inheritDoc}
     */
    public function __toString() {
        return $this->getOutput();
    }
}

<?php

namespace System;

class File {
    /**
     * Directory separator
     * @const string
     */
    const DS = DIRECTORY_SEPARATOR;
    /**
     * Root path ( C:\xampp\htdocs\MVC_Blog_System )
     * @var string 
     */
    private $root;
    
    /**
     * Constructor
     * @param string $root
     */
    public function __construct($root){
        $this->root = $root;
    }
    /**
     * Determine if the file exists
     * @param string $file
     * @return bool
     */
    public function exists($file) {
        return file_exists($this->to($file));
    }
    /**
     * Require the given file
     * @param string $file
     * @return mixed
     */
    public function call($file) {
        return require $this->to($file);
    }
    /**
     * Generate class full path in vendor
     * @param string $path 
     * @return string
     */
    public function toVendor($path){
        return $this->to('vendor/' . $path);
    }
    /**
     * Generate full class path.
     * @param string $path 
     * @return string
     */
    public function to($path){
        return $this->root . static::DS . str_replace(['/', '\\'], static::DS, $path);
    }
}

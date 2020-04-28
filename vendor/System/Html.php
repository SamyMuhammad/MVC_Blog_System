<?php

namespace System;

class Html {
    /**
     * Application object
     * @var \System\Application
    */
    protected $app;
    
    /**
     * HTML title
     * @var string 
     */
    private $title;
    
    /**
     * HTML description
     * @var string 
     */
    private $description;
    
    /**
     * HTML keywords
     * @var string 
     */
    private $keywords;

    /*
     * Constructor
     * @param \System\Application $app
     * @return object
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * Set title
     * @param string $title
     * @return void
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    /**
     * Get title
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    /**
     * Set description
     * @param string $description
     * @return void
     */
    public function setDescription($description) {
        $this->descriptipn = $description;
    }
    /**
     * Get description
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    /**
     * Set keywords
     * @param string $keywords
     * @return void
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }
    /**
     * Get keywords
     * @return string
     */
    public function getKeywords() {
        return $this->keywords;
    }
}

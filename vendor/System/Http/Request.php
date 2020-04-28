<?php

namespace System\Http;

class Request {
    /**
     * URL
     * @var string
     */
    private $url;
    /**
     * Base URL
     * @var string
     */
    private $baseUrl;
    
    /**
     * prepare URL
     * Analyze the URL and store its components in Request properties.
     * @return void
     */
    public function prepareUrl() {
        $script = dirname($this->server('SCRIPT_NAME')); // /MVC_Blog_System       
        $requestUri = $this->server('REQUEST_URI');     // parentFolder/currentFile.php?id=5
        if (strpos($requestUri, '?') !== false){
            list($requestUri, $queryString) = explode('?', $requestUri);
        }
        $this->url = preg_replace('#^'.$script.'#', '', $requestUri); //Every thing after the root path (MVC_Blog_System) i.e. /home/post/54545
        $this->baseUrl = $this->server('REQUEST_SCHEME') . '://' . $this->server('HTTP_HOST') . $script . '/'; //Full dynamic root path.
    }
    /**
     * Get a value from _GET by the given key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default= null) {
        return array_get($_GET, $key, $default);
    }
    /**
     * Get a value from _POST by the given key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function post($key, $default= null) {
        return array_get($_POST, $key, $default);
    }
    /**
     * Get a value from _SERVER by the given key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function server($key, $default= null) {
        return array_get($_SERVER, $key, $default);
    }
    /**
     * Get current Request Method
     * @return string 
     */
    public function method() {
        return $this->server('REQUEST_METHOD');
    }
    /**
     * Get full URL of the script
     * @return string
     */
    public function baseUrl() {
        return $this->baseUrl;
    }
    /**
     * Get only relative URL (clean URL)
     * @return string
     */
    public function url() {
        return $this->url;
    }
}

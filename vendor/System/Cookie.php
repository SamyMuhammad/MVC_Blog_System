<?php

namespace System;

class Cookie {
    /**
     * Object from class Application 
     * @var \System\Application
     */
    private $app;
    
    /**
     * Constructor
     * @param \System\Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * Set new value to cookie
     * @param string $key
     * @param mixed $value
     * @param int $hours
     * @return void
     */
    public function set($key, $value, $hours = 1800) {
        setcookie($key, $value, time() + $hours * 3600, '', '', false, true);
    }
    /**
     * Get value from Cookie by the given key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default= null) {
        return array_get($_Cookie, $key, $default);
    }
    /**
     * Determine if the Cookie has the given key
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return array_key_exists($key, $_COOKIE);
    }
    /**
     * Remove the given key from Cookie
     * @param string $key
     * @return void
     */
    public function remove($key) {
        setcookie($key, null, -1);
        unset($_Cookie[$key]);
    }
    /**
     * Get all Cookie data
     * @return array
     */
    public function all() {
        return $_Cookie;
    }
    /**
     * Destroy Cookie
     * @return void 
     */
    public function destroy() {
        foreach (array_keys($this->all()) as $key){
            $this->remove($key);
        }
        unset($_COOKIE);
    }
}

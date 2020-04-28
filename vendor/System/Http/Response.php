<?php

namespace System\Http;
use System\Application;
/**
 * Description of Response
 *
 * @author Samy
 */
class Response {
    /**
     * Application object
     * @var \System\Apllication
     */
    private $app;
    
    /**
     * Headers container that will be sent to the browser.
     * @var array
     */
    private $headers = [];
    
    /**
     * The content that will be sent to the browser.
     * @var string
     */
    private $content = '';
    
    
    /**
     * Constructor
     * @param \System application $app
     */
    public function __construct(Application $app){
        $this->app = $app;
        
    }
    /**
     * Set the response output content.
     * @param string $content
     * @return void
     */
    public function setOutput($content) {
        $this->content = $content;
    }
    /**
     * Set the response headers
     * @param string $header
     * @param mixed $value
     * @return void
     */
    public function setHeader($header, $value) {
        $this->headers[$header] = $value;
    }
    /**
     * Send the response headers and content
     * @return void
     */
    public function send() {
        $this->sendHeaders();
        $this->sendOutput();
    }
    /**
     * Send the response headers
     * return void
     */
    private function sendHeaders() {
        foreach ($this->headers as $header => $value){
            header( $header . ':' . $value);
        }
    }
    /**
     * Send the response output
     * return void
     */
    private function sendOutput() {
        echo $this->content;
    }
}

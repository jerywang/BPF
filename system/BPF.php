<?php
/**
 * $Id: BPF.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 */
final class BPF {
    /**
     * @return BPF The instance of BPF
     */
    public static function &getInstance() {
        if (! self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $instance;

    private function __construct() {}

    public function run() {
        $this->request = new $this->requestClass();
        $this->response = new $this->responseClass();
        $this->dispatch();
    }

    public function dispatch(){
    	$this->running = true;
    	register_shutdown_function(array(&$this, "shutdown"));
        $router = new $this->routerClass();
        $class = $router->mapping();
        $interceptor = $this->getInterceptor();
        $interceptor->before();
        $controller = $this->getController($class);
        $this->currentController = $class;
        $page = $controller->execute();
        if ($this->getResponse()->isAjax){
            header('Content-type: application/json');
            echo json_encode($this->getResponse()->getAttr());
        }
        else {
            BPF::getInstance()->getResponse()->render($page);
        }
        $interceptor->after();
        $this->running = false;
    }

    public function getConfig($name = 'Config_Common') {
    	if (empty($this->configures[$name])){
    		$this->configures[$name] = $this->loadConfig($name);
    	}
    	return $this->configures[$name];
    }

    public function loadConfig($name) {
    	$config = $name::$config;
        return $config;
    }
    
    private $configures = array ();
    
    protected function getInterceptor($class="WEBInterceptor", $path="interceptor") {
    	if(!$this->interceptor){
    		$this->interceptor = new $class();
    	}
    	return $this->interceptor;
    }
    
    private $interceptor = null;
    
    /**
     * @param string $class
     * @return Controller
     */
    public function getController($class = 'Controller_404') {
        if (empty($this->controllers[$class])) {
	        $this->controllers[$class] = $this->loadController($class);
        }
        return $this->controllers[$class];
    }

    /**
     * @param string $className
     * @return Controller
     */
    public function loadController($className) {
        error_log($className);
        $controller = new $className();
        return $controller;
    }

    /**
     * @return Controller
     */
    public function getCurrentController(){
        return $this->currentController;
    }

    private $controllers = array();

    public $currentController = null;

    /**
     * @var Router
     */
    private $router;

    public function setRouterClass($class) {
        $this->routerClass = $class;
    }

    /**
     * @var Request
     */
    private $request;

    public function setRequestclass($class) {
        $this->requestClass = $class;
    }

    /**
     * @var Response
     */
    private $response;

    public function setResponseClass($class) {
        $this->responseClass = $class;
    }

    private $routerClass = "Router";
    private $requestClass = "Request";
    private $responseClass = "Response";

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }

    public function shutdown() {
        if ($this->running) {
            Logger::warning('[error]: system is exception (*>﹏<*) !');
        }
    }
    
    private $running = true;

}
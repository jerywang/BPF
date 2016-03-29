<?php

/**
 * $Id: BPF.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 */
final class BPF {

    private static $instance;

    public $currentController = null;

    private $configures = array();

    private $interceptor = null;

    private $controllers = array();

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Router
     */
    private $routerClass = "Router";

    /**
     * @var Request
     */
    private $requestClass = "Request";

    /**
     * @var Response
     */
    private $responseClass = "Response";

    private $running = true;

    private function __construct() {
    }

    public function run() {
        try {
            $this->request = new $this->requestClass();
            $this->response = new $this->responseClass();
            $this->dispatch();
        } catch (Exception $e) {
            $errMsg = $e->getMessage();
            list($sysMsg, $apiMsg) = explode('|', $errMsg);
            $errMsg =  'errCode[' . $e->getCode() . '], errMessage[' . $apiMsg . ']';
            Log::fatal($errMsg);
            echo $errMsg;
        }
    }

    public function dispatch() {
//        $this->running = true;
//        register_shutdown_function(array(&$this, "shutdown"));
        /**
         * @var $router Router
         */
        $router = new $this->routerClass();
        $class = $router->mapping();
        /**
         * @var Interceptor $interceptor
         */
        $interceptor = $this->getInterceptor();
        $interceptor->before();
        $controller = $this->getController($class);
        $this->currentController = $class;
        $page = $controller->execute();
        if ($this->getResponse()->isJson) {
            header('Content-type: application/json');
            echo json_encode($this->getResponse()->getAttr());
        } else {
            BPF::getInstance()->getResponse()->render($page);
        }
        $interceptor->after();
//        $this->running = false;
    }
    protected function getInterceptor($class = "WEBInterceptor", $path = "interceptor") {

        if (!$this->interceptor) {
            $this->interceptor = new $class();
        }
        return $this->interceptor;
    }

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
        $controller = new $className();
        return $controller;
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @return BPF The instance of BPF
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConfig($name = 'Config_Common', $key = '') {
        if (empty($this->configures[$name])) {
            $this->configures[$name] = $this->loadConfig($name, $key);
        }
        return $this->configures[$name];
    }

    public function loadConfig($name, $key = '') {
        $config = $name::$config;
        return empty($key) ? $config : $config[$key];
    }

    /**
     * @return Controller
     */
    public function getCurrentController() {
        return $this->currentController;
    }

    public function setRouterClass($class) {
        $this->routerClass = $class;
    }

    public function setRequestClass($class) {
        $this->requestClass = $class;
    }

    public function setResponseClass($class) {
        $this->responseClass = $class;
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

//    public function shutdown() {
//        if ($this->running) {
//            Logger::warning('[error]: system is exception (*>﹏<*) ! [time]: '.date('Y-m-d H:i:s'));
//        }
//    }

    /**
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }

}
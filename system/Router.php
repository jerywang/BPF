<?php
/**
 * $Id: Route.php 2014-6-30 wangguoxing (wangguoxing@system.com) $
 */
class Router {
    
    /**
     * @return Service_Page_Base
     */
    public function mapping(){
        $mappings = BPF::getInstance()->getConfig('Config_Route');
        $uri = $_SERVER ['REQUEST_URI'];
        $pos = strpos ( $uri, '?' );
        if ($pos) {
            $uri = substr ( $uri, 0, $pos );
        }
        if (empty ( $uri )) {
            $uri = '/';
        }
        $matches = array ();
        foreach ( $mappings as $class => $mapping ) {
            foreach ( $mapping as $pattern ) {
                if (@ereg ( $pattern, $uri, $matches )) {
                    BPF::getInstance()->getRequest()->setRouterMatches($matches);
                    return $class;
                }
            }
        }
        return 'Controller_404';
    }
}
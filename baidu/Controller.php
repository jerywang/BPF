<?php
/**
 * $Id: Controller.php Jul 4, 2014 wangguoxing (wangguoxing@baidu.com) $
 */
abstract class Controller {
    
    abstract public function execute();
    
    public function setAttr($name, $value) {
        BPF::getInstance()->getResponse()->setAttr($name, $value);
    }
    
    public function getAttr($name = null) {
        return BPF::getInstance()->getResponse()->getAttr($name);
    }
    
}
<?php
/**
 * Cache_Memcached
 *
 */
class Cache_Memcached extends Memcached {

    /**
     * cache 功能是否开启
     * 作用域是 $this 以及与当前实例相关的dao实例
     * @var boolean
     */
    public $enableCache = true;

    public function enableCache ($boolean) {
        $this->enableCache = $boolean;
    }

    public function setEnable ($boolean) {
        $this->enableCache = $boolean;
    }
    /*********memcached写法********/
//    public function _addServers(){
//        $cache_config = BPF::getInstance()->getConfig('cache');
//        $this->addServers($cache_config['cache']['servers']);
//    }
    public function _addServers(){
        $cache_config = BPF::getInstance()->getConfig('Config_Cache');
        $this->addServers($cache_config['memcache']);
    }
}

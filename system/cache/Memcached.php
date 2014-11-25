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
}

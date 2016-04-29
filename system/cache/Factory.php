<?php

/**
 * Cache_Factory
 */
class Cache_Factory {

    private static $instance;

    /**
     * cache 功能是否开启
     * @var boolean
     */
    public $enableCache = true;
    private $redisList = array();
    private $memcache;

    private function __construct() {
    }

    /**
     * @return Cache_Factory
     */
    public static function &getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getRedis($name = null) {
        if (!isset($this->redisList[$name])) {
            $this->redisList[$name] = $this->loadRedis($name);
        }
        return $this->redisList[$name];
    }

    /**
     * return new instance of redis
     * @param string $name
     * @return redis
     */
    public function loadRedis($name) {
        $cacheConfig = BPF::getInstance()->getConfig('Config_Cache');
        $server = $cacheConfig['redis'][$name];
        $redis = new Redis();
        $redis->connect($server['ip'], $server['port'], $server['timeout']);
        return $redis;
    }

    public function getMemcache() {
        if (!isset($this->memcache)) {
            $this->memcache = $this->loadMemcache();
        }
        return $this->memcache;
    }

    /**
     * return new instance of Memcache
     *
     * @return Memcache
     */
    public function loadMemcache() {
        $cacheConfig = BPF::getInstance()->getConfig('Config_Cache');
        $memcache = new Memcached();
        $memcache->addServers($cacheConfig['memcache']);;
        return $memcache;
    }

    public function enableCache($boolean) {
        $this->enableCache = $boolean;
    }

    public function setEnable($boolean) {
        $this->enableCache = $boolean;
    }

}
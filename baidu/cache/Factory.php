<?php
/**
 * Cache_Factory
 */
class Cache_Factory {
    /**
     * @return Cache_Factory
     */
    public static function &getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $instance;
    
    private $redisList = array();

    public function getRedis($name=null) {
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
		$cache_config = BPF::getInstance()->getConfig('Config_Cache');
		$server = $cache_config['redis'][$name];
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
	 * return new instance of redis
	 * @param string $name
	 * @return redis
	 */
	public function loadMemcache() {
		$cache_config = BPF::getInstance()->getConfig('Config_Cache');
		$memcache = new Memcached();
		$memcache->addServers($cache_config['memcache']);;
		return $memcache;
	}
	
	private $memcache;
	
	/**
	 * cache 功能是否开启
	 * @var boolean
	 */
	public $enableCache = true;
	
	public function enableCache ($boolean) {
		$this->enableCache = $boolean;
	}
	
	public function setEnable ($boolean) {
		$this->enableCache = $boolean;
	}

    private function __construct() {}

}
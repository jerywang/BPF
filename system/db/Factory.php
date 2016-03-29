<?php

/**
 * DB_Factory
 */
class DB_Factory {

    private static $instance;

    /**
     * @var array
     */
    public $pdoList = array();

    /**
     * @var Db_Pdo
     */
    private $pdoClass = 'Db_Pdo';

    private function __construct() {
    }

    /**
     * @return DB_Factory
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $class
     */
    public function setPdoClass($class) {
        $this->pdoClass = $class;
    }

    /**
     * @breif 获取pdo实例
     *
     * @param string $name 集群名称
     * @param bool $isMaster
     *
     * @return DB_PDO
     */
    public function getPdo($name = null, $isMaster = false) {
        if (!isset($this->pdoList[$name])) {
            $this->pdoList[$name] = $this->loadPdo($name, $isMaster);
        }
        return $this->pdoList[$name];
    }

    /**
     * @breif  return new instance of pdo
     *
     * @param  string $name
     * @param  bool $isMaster
     *
     * @return Db_Pdo
     */
    public function loadPdo($name = null, $isMaster = false) {
        $cluster = $this->getClusterConf($name);
        $pdo = $this->connect($cluster, $isMaster);
        return $pdo;
    }

    /**
     * @param $name
     *
     * @return null|array
     *
     * @throws Exception
     */
    protected function getClusterConf($name) {
        $dbconf = BPF::getInstance()->getConfig('Config_Database', $name);
        if (empty($dbconf)) {
            //trigger_error('Config_Database:.'.$name.' error', E_USER_ERROR);
            throw new Exception(
                Const_CodeMessage::getErrMsgByCode(Const_CodeMessage::ERR_SYS_DB_CONF),
                Const_CodeMessage::ERR_SYS_DB_CONF
            );
        }
        return $dbconf;
    }

    /**
     * 数据库连接
     *
     * @param $cluster
     * @param $isMaster
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function connect(&$cluster, $isMaster) {
        do {
            try {
                $conf = $this->getOneHost($cluster, $isMaster);
                if (empty($conf['slave'])) {
                    throw new Exception(
                        Const_CodeMessage::getErrMsgByCode(Const_CodeMessage::ERR_SYS_DB_CONNECT_FAILED),
                        Const_CodeMessage::ERR_SYS_DB_CONNECT_FAILED
                    );
                }
                $dsn = 'mysql:host=' . $conf['host']['host'] . ';port=' . $conf['host']['port'] . ';dbname=' . $conf['dbname'] . ';charset=' . $conf['charset'];
                /**
                 * @var $pdo Db_PDO
                 */
                $pdo = new $this->pdoClass($dsn, $conf['username'], $conf['password'], isset($conf['driver_options']) ? $conf['driver_options'] : array());
                if (isset($conf['default_fetch_mode'])) {
                    $pdo->setDefaultFetchMode($conf['default_fetch_mode']);
                }
                if ($conf['init_statements']) {
                    foreach ($conf['init_statements'] as $sql) {
                        $pdo->exec($sql);
                    }
                }
                return $pdo;
            } catch (Exception $e) {
                if ($e->getCode() == 100) {
                    throw new Exception($e->getMessage(), $e->getCode());
                }
                //剔除有故障slave
                foreach ($cluster['slave'] as $key => $host) {
                    if ($conf['host']['host'] == $host['host']) {
                        unset($cluster['slave'][$key]);
                    }
                }
            }
        } while (true);
    }

    /**
     * @param  $cluster
     * @param  bool $isMaster
     *
     * @return array
     */
    protected function getOneHost(&$cluster, $isMaster = false) {
        if ($isMaster) {
            $cluster['host'] = $cluster['master'];
        } else {
            $cluster['host'] = $this->randBalance($cluster['slave']);
        }
        return $cluster;
    }

    /**
     * @brief 随机负载均衡
     *
     * @param array $slave
     *
     * @return array
     *
     * @todo 根据slave的连接数负载均衡
     */
    protected function randBalance($slave) {
        return $slave ? $slave[array_rand($slave)] : array();
    }

    /**
     * @param string $name
     */
    public function closePdo($name = 'default') {
        if (!isset($this->pdoList[$name])) {
            return;
        }
        unset($this->pdoList[$name]);
    }

    public function closePdoAll() {
        if (!empty($this->pdoList)) {
            foreach (array_keys($this->pdoList) as $name) {
                unset($this->pdoList[$name]);
            }
        }
    }


}

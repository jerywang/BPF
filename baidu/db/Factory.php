<?php
/**
 * DB_Factory
 */
class DB_Factory {
    /**
     * @return DB_Factory
     */
    public static function &getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $instance;

    /**
     * Returns pdo instance by given name. only one instance of pdo will be created for one name.
     * @param string $name
     * @return DB_PDO
     */
    public function getPdo($name=null) {
        if (!isset($this->pdoList[$name])) {
            $this->pdoList[$name] = $this->loadPdo($name);
        }
        return $this->pdoList[$name];
    }

    /**
     * return new instance of pdo
     * @param string $name
     * @return Db_Pdo
     */
    public function loadPdo($name=null) {
        $_dbcfg = BPF::getInstance()->getConfig('Config_Database');
        $dbcfg = $_dbcfg[$name];
        $pdo = new $this->pdoClass(
            $dbcfg['dsn'],
            @$dbcfg['username'],
            @$dbcfg['password'],
            isset($dbcfg['driver_options']) ? $dbcfg['driver_options'] : array());

        $pdo->setName($name);

        if (isset($dbcfg['default_fetch_mode'])) {
            $pdo->setDefaultFetchMode($dbcfg['default_fetch_mode']);
        }

        if (isset($dbcfg['init_statements'])) {
            foreach ($dbcfg['init_statements'] as $sql) {
                $pdo->exec($sql);
            }
        }
        return $pdo;
    }

    public function closePdo($name='default') {
        if (!isset($this->pdoList[$name])) {
            return;
        }
        unset($this->pdoList[$name]);
    }

    public $pdoList = array();

    public function closePdoAll() {
        if(!empty($this->pdoList)) {
            foreach(array_keys($this->pdoList) as $name) {
                unset($this->pdoList[$name]);
            }
        }
    }

    private function setPdoClass($class) {
        $this->pdoClass = $class;
    }

    private $pdoClass = 'Db_Pdo';

    private function __construct() {
    }

}

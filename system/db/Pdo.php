<?php
/**
 * Db_Pdo
 */
class Db_Pdo extends PDO {
    private $i = 0;
    public function __construct($dsn, $username="", $password="", $driver_options=array()) {
        parent::__construct($dsn, $username, $password, $driver_options);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    public function exec($statement) {
        $stmt = parent::exec($statement);
        if ($stmt instanceof PDOStatement) {
            $stmt->setFetchMode($this->default_fetch_mode);
        } else {
            $error_info = parent::errorInfo();
            if (parent::errorCode() !== '00000') {
                trigger_error($statement.' | '.join(' | ',$error_info),E_USER_ERROR);
            }
        }
        return $stmt;
    }

    public function prepare($statement, $driver_options=array()) {
        $stmt = parent::prepare($statement, $driver_options);
        if ($stmt instanceof PDOStatement) {
            $stmt->setFetchMode($this->default_fetch_mode);
        }
        return $stmt;
    }

    public function query($statement, $pdo=NULL, $object=NULL) {
        if($pdo != NULL && $object != NULL){
            $stmt = parent::query($statement, $pdo, $object);
        }else{
            $stmt = parent::query($statement);
        }
            if ($stmt instanceof PDOStatement) {
            $stmt->setFetchMode($this->default_fetch_mode);
        }
        return $stmt;
    }

    public function setName($name) {
        $this->name = $name;
        $this->config = BPF::getInstance()->getConfig("Config_Database");
    }

    public function getName() {
        return $this->name;
    }

    private $name;

    public $config;

    public function setDefaultFetchMode($mode) {
        $this->default_fetch_mode = $mode;
    }

    private $default_fetch_mode = PDO::FETCH_BOTH;
}

<?php

/**
 * Class Db_Pdo
 */
class Db_PDO extends PDO {

    private $defaultFetchMode = PDO::FETCH_BOTH;

    public function __construct($dsn, $user = "", $passWd = "", $driverOptions = array()) {
        parent::__construct($dsn, $user, $passWd, $driverOptions);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    public function exec($statement) {
        $stmt = parent::exec($statement);
        if ($stmt instanceof PDOStatement) {
            $stmt->setFetchMode($this->defaultFetchMode);
        } else {
            $error_info = parent::errorInfo();
            if (parent::errorCode() !== '00000') {
                trigger_error($statement . ' | ' . join(' | ', $error_info), E_USER_ERROR);
            }
        }
        return $stmt;
    }

    public function prepare($statement, $driver_options = array()) {
        $stmt = parent::prepare($statement, $driver_options);
        if ($stmt instanceof PDOStatement) {
            $stmt->setFetchMode($this->defaultFetchMode);
        }
        return $stmt;
    }

    public function query($statement, $pdo = NULL, $object = NULL) {
        if ($pdo != NULL && $object != NULL) {
            $stmt = parent::query($statement, $pdo, $object);
        } else {
            $stmt = parent::query($statement);
        }
        if ($stmt instanceof PDOStatement) {
            $stmt->setFetchMode($this->defaultFetchMode);
        }
        return $stmt;
    }

    public function setDefaultFetchMode($mode) {
        $this->defaultFetchMode = $mode;
    }

}

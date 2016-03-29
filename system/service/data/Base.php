<?php

/**
 * $Id: Base.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 * class Service_Data_Base
 */
abstract class Service_Data_Base {

    private $daoList = null;

    public function __construct() {
    }

    /**
     *
     * @param $daoName - string
     *
     * @return Dao_Base [one instance of Dao_Base]
     */
    public function getDao($daoName) {
        if (!isset($this->daoList[$daoName])) {
            $this->daoList[$daoName] = new $daoName();
        }
        return $this->daoList[$daoName];
    }
}

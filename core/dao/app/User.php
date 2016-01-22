<?php

class Dao_App_User extends Dao_Base {

    public function getTablePK() {
        return 'id';
    }

    public function getTableName() {
        return 'user';
    }

    public function getReadPdoName() {
        return "slave";
    }

    public function getWritePdoName() {
        return "master";
    }

}
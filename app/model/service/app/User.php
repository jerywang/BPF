<?php

class Service_App_User extends Service_Data_Base {

    public function getUserInfo($uid) {
        $userInfo = $this->getDefaultDao()->select(array('id' => $uid));
        return $userInfo;
    }

    /**
     * 获取默认DAO
     * @return Dao_App_User
     */
    public function getDefaultDao() {
        return $this->getDao('Dao_App_User');
    }

    public function getUserList($conds = array(), $orderBy = null, $limit = 100, $offset = 0) {
        $userList = $this->getDefaultDao()->select($conds, $orderBy, $limit, $offset);
        return $userList;
    }

    public function findCount($conds = array(), $field = '') {
        return $this->getDefaultDao()->selectCount($conds, $field);
    }

    public function insert($data) {
        $rs = $this->getDefaultDao()->insert($data);
        return $rs;
    }

}


<?php
class Service_Data_App_User extends Service_Data_Base {
    
    public function getUserInfo($uid) {
        $userInfo = $this->getDefaultDao()->findById($uid);
        return $userInfo;
    }
    
    public function getUserList($conds = array(), $orderBy = null, $limit = 100, $offset =0) {
        $userList = $this->getDefaultDao()->findAssoc($conds);
        return $userList;
    }
    
    public function findCount($conds = array(), $field = ''){
        return $this->getDefaultDao()->findCount($conds, $field);
    }
    
    public function insert($data){
        $rs = $this->getDefaultDao()->insert($data);
        return $rs;
    }
    
    /**
     * 获取默认DAO
     * @return Ambigous <DAO, NULL>
     */
    public function getDefaultDao() {
        return $this->getDao('Dao_App_User');
    }
    
}
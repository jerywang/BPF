<?php
/**
 * class Service_Page_App_User
 * 获取用户信息
 * @author jerry
 */
class Service_Page_App_User extends Service_Page_Base {
    
    public function execute($param) {
        $userData = new Service_Data_App_User();
        $userInfo = $userData->getUserInfo($param['uid']);
        return $userInfo;
    }
    
}
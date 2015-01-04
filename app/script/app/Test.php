<?php
require 'Init.php';

class Script_App_Test {
    
    public function execute(){
        $userPage = new Service_App_User();
        print_r($userPage->getUserInfo(1));
        
//        $coreUserPage = new Service_Page_App_User();
//        print_r($coreUserPage->execute(array("uid"=>2)));
    }
    
}

$script = new Script_App_Test();
$script->execute();
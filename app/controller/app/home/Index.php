<?php

/**
 * $Id: Index.php Jul 4, 2014 wangguoxing (wangguoxing@system.com) $
 */
class Controller_App_Home_Index extends Controller_App_Base {

    public function call() {
        //from self data
        $msUser = new Service_App_User();
        $userInfo = $msUser->getUserInfo(2);

        //from core api data
        $userPage = new Service_Page_App_User();
        $user = $userPage->execute(array('uid' => 1));

        $this->setAttr('userInfo', $userInfo);
        $this->setAttr('user', $user);

        //send string
        $this->setAttr('header', 'header component');
        $this->setAttr('footer', 'footer component');

        //send array
//         $this->setAttr('header', array('header'=>'header component','id'=>11));
//         $this->setAttr('footer', array('footer'=>'footer component','id'=>22));

        //send object
//         $std = new stdClass();
//         $std->id=123;
//         $std->name='wqwq';
//         $this->setAttr('header', $std);

        echo BPF::getInstance()->getCurrentController() . '<br>';
        return 'App_Home_Index';
    }

    /**
     * @return string
     */
    protected function getClass() {
        return __CLASS__;
    }
}
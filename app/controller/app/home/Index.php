<?php

/**
 * $Id: Index.php Jul 4, 2014 wangguoxing (wangguoxing@system.com) $
 */
class Controller_App_Home_Index extends Controller_App_Base {

    public function call() {
//        BPF::getInstance()->getRequest()->userId=10;
//        echo BPF::getInstance()->getRequest()->userId;
//        BPF::getInstance()->getRequest()->setUserInfo(array("bduss"=>"aaaqwqw11121"));
//        print_r(BPF::getInstance()->getRequest()->getUserInfo());
        //from self data
        $user = new Service_App_User();
        $userInfo = $user->getUserInfo(1);

        $this->setAttr('userInfo', $userInfo);

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
        echo Util_Date::getDate();
        print_r(BPF::getInstance()->getRequest()->getRouterMatches());
        return 'App_Home_Index';
    }

    /**
     * @return string
     */
    protected function getClass() {
        return __CLASS__;
    }
}
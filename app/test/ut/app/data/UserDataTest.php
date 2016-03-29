<?php
require '/home/wangguoxing/odp/app/BPF/app/test/ut/Init.php';
/**
 * UserDataTest.php 2016-03-29 13:16
 * User: wangguoxing@baidu.com
 * Description:
 */
class UserDataTest extends PHPUnit_Framework_TestCase {
    /**
     * @var Service_App_User
     */
    private $service;

    public function setUp() {
        $this->service = new Service_App_User();
        parent::setUp();
    }

    public function testGetUserInfo() {
        $result = $this->service->getUserInfo(1);
        $this->assertEquals(1, count($result));
    }

}

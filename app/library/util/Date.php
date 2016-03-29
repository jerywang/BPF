<?php
/**
 * Date.php 2016-03-29 16:09
 * User: wangguoxing@baidu.com
 * Description:
 */
class Util_Date {

    private function __construct() {
    }

    public static function getDate() {
        return date('Y-m-d');
    }
}
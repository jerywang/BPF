<?php

/**
 * $Id CodeMessage.php 2015-01-04 11:01 wangguoxing@baidu.com $
 */
abstract class Const_CodeMessage {

    const ERR_SYS_OK = 0;
    const ERR_SYS_CLASS_NOT_FOUND = 100;
    const ERR_SYS_DB_CONF = 200;
    const ERR_SYS_DB_CONNECT_FAILED = 201;

    /**
     * @param int $code
     * @return string
     */
    public static function getErrMsgByCode($code) {
        $errMap = self::getErrMap();
        return $errMap[$code] ? $errMap[$code] : '';
    }

    /**
     * @return array
     */
    public static function getErrMap() {
        return array(
            self::ERR_SYS_CLASS_NOT_FOUND => 'class not found|类加载失败',
            self::ERR_SYS_DB_CONF => 'database conf error|数据库配置错误',
            self::ERR_SYS_DB_CONNECT_FAILED => 'database connect refused|数据库失败',
        );
    }
}
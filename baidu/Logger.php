<?php
/**
 * $Id: Logger.php Jul 2, 2014 wangguoxing (wangguoxing@baidu.com) $
 */
class Logger {

    /**
     * @param name string
     * @param messages string
     */
    public static function debug() {
        $args = func_get_args();
        $args = array_merge(array(LOG_DEBUG), $args);
        return self::log($args);
    }

    /**
     * @param name string
     * @param messages string
     */
    public static function info() {
        $args = func_get_args();
        $args = array_merge(array(LOG_INFO), $args);
        return self::log($args);
    }

    /**
     * @param name string
     * @param messages string
     */
    public static function notice() {
        $args = func_get_args();
        $args = array_merge(array(LOG_NOTICE), $args);
        return self::log($args);
    }

    /**
     * @param name string
     * @param messages string
     */
    public static function warning() {
        $args = func_get_args();
        $args = array_merge(array(LOG_WARNING), $args);
        return self::log($args);
    }

    /**
     * @param name string
     * @param messages string
     */
    public static function error() {
        $args = func_get_args();
        $args = array_merge(array(LOG_ERR), $args);
        return self::log($args);
    }

    /**
     * @param name string
     * @param messages string
     */
    public static function fatal() {
        $args = func_get_args();
        $args = array_merge(array(LOG_CRIT), $args);
        return self::log($args);
    }

    /**
     * @param priority int
     * @param name string
     * @param messages string
     */
    private static function log($args) {
        file_put_contents(ROOT_PATH.'BPF.log', '[msg]: '.$args[1].PHP_EOL, FILE_APPEND);
        return true;
    }

    private $priority;
    private $priorities = array();
}

<?php

/**
 * $Id: Logger.php Jul 2, 2014 wangguoxing (554952580@qq.com) $
 */
class Log {

    protected static $properties = array(
        0 => 'debug',
        1 => 'info',
        2 => 'notice',
        3 => 'warning',
        4 => 'error',
    );

    private function __construct() {
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function debug($str) {
        return self::log($str, 0);
    }

    /**
     * @param  string $str
     * @param  int $property
     * @return bool
     */
    private static function log($str, $property) {
        $logFile = ROOT_PATH . 'log/' . self::$properties[$property] . '.' . date('YmdH') . '.log';
        file_put_contents($logFile, $str . PHP_EOL, FILE_APPEND);
        return true;
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function info($str) {
        return self::log($str, 1);
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function notice($str) {
        return self::log($str, 2);
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function warning($str) {
        return self::log($str, 3);
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function error($str) {
        return self::log($str, 4);
    }

    /**
     * @param  string $str
     * @return bool
     */
    public static function fatal($str) {
        return self::log($str, 5);
    }
}

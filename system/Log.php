<?php

/**
 * $Id: Logger.php Jul 2, 2014 wangguoxing (554952580@qq.com) $
 */
class Log {

    protected static $logStr = array(
        'debug' => array(),
        'info' => array(),
        'notice' => array(),
        'warning' => array(),
        'error' => array(),
    );

    private function __construct() {
    }

    /**
     * @return bool
     */
    public static function log() {
        foreach (self::$logStr as $type => $logs) {
            if(!empty($logs)) {
                $logFile = ROOT_PATH . 'log/' . $type . '.' . date('YmdH') . '.log';
                $str = strtoupper($type) . ': ' . date('Y-m-d H:i:s') . ' [' . BPF::getInstance()->currentController . '] ' . implode(', ', $logs);
                file_put_contents($logFile, $str . PHP_EOL, FILE_APPEND);
            }
        }
        return true;
    }

    /**
     * @param  string $str
     */
    public static function debug($str) {
        self::$logStr['debug'][] = $str;
    }

    /**
     * @param  string $str
     */
    public static function info($str) {
        self::$logStr['info'][] = $str;
    }

    /**
     * @param  string $str
     */
    public static function notice($str) {
        self::$logStr['notice'][] = $str;
    }

    /**
     * @param  string $str
     */
    public static function warning($str) {
        self::$logStr['warning'][] = $str;
    }

    /**
     * @param  string $str
     */
    public static function error($str) {
        self::$logStr['error'][] = $str;
    }

    /**
     * @param  string $str
     */
    public static function fatal($str) {
        self::$logStr['fatal'][] = $str;
    }
}

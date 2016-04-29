<?php

class Util_Compress {

    /**
     * css文件过期时间,默认为12小时
     */
    public $lifeTime = 43200;

    public function __construct() {
        header("cache-control: must-revalidate");
        $expire_time = $this->getTime();
        $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $expire_time) . " GMT";
        header($expire);
        ob_start("self::compress");
    }

    public function getTime() {
        return $this->lifeTime;
    }

    public function setTime($time) {
        $this->lifeTime = $time;
    }

    public function compress($buffer) {
        //去掉注释
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        //去掉\r,\n,\t,空格等
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
        return $buffer;
    }

    public function import($files) {
        try {
            if (is_array($files)) {
                foreach ($files as $file) {
                    include($file);
                }
            } else {
                include($files);
            }
        } catch (Exception $e) {
            Log::warning("import file failed:" . $files);
        }
    }

    public function end() {
        ob_end_flush();
    }

//	  unset()时候生效
//    public function __destruct(){
//        ob_end_flush();
//    }
}
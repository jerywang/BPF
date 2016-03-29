<?php

class Util_Code {
    /**
     * 加密函数
     * @param   $str String 加密前的字符串
     * @param   $key  integer 密钥
     * @return  String  加密后的字符串
     */
    public static function encrypt($str, $key = 123456) {
        $coded = '';
        $keyLength = strlen($key);

        for ($i = 0, $count = strlen($str); $i < $count; $i += $keyLength) {
            $coded .= substr($str, $i, $keyLength) ^ $key;
        }

        return str_replace('=', '', base64_encode($coded));
    }

    /**
     * 解密函数
     * @param   string $str 加密后的字符串
     * @param   integer $key 密钥
     * @return  string  加密前的字符串
     */
    public static function decrypt($str, $key = 123456) {
        $coded = '';
        $keyLength = strlen($key);
        $str = base64_decode($str);

        for ($i = 0, $count = strlen($str); $i < $count; $i += $keyLength) {
            $coded .= substr($str, $i, $keyLength) ^ $key;
        }

        return $coded;
    }
}

?>
<?php
class Util_String {
    /**
     * 合法邮箱验证
     * @param unknown_type $email
     * @return bool
     */
    public static function is_valid($email) {
        if (preg_match ( '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i', $email )) {
            return true;
        } else
            return false;
    }
    
    /**
     * 返回字符串数量函数
     * @param string $str
     * @return int
     */
    
    public static function getCutStr($str, $charset = "utf-8") {
        $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        return preg_match_all ( $re [$charset], $str, $match );
    }
    
    /**
     * 截取字符串函数
     * @param string $str
     * @param int $length
     * @param int $start
     * @param bool $suffix
     * @return string $slice
     */
    public static function cutStr($str, $length, $start = 0, $charset = "utf-8", $suffix = true)
    {
        if (function_exists ( "mb_substr" )){
            if (mb_strlen ( $str, $charset ) <= $length){
                return $str;
            }
            $slice = mb_substr ( $str, $start, $length, $charset );
        }
        else {
            $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all ( $re [$charset], $str, $match );
            if (count ( $match [0] ) <= $length)
                return $str;
            $slice = join ( "", array_slice ( $match [0], $start, $length ) );
        }
        if ($suffix)
        {
            if (strlen ( $slice ) < strlen ( $str ))
            {
                $slice .= "…";
            }
        }
        return $slice;
    }
    
    /**
     * 获取get，post提交参数值
     * @param unknown_type $strParam
     * @param bool $set_var
     */
    public static function q($strParam, $set_var = false) {
        $value = "";
        $var = array ();
        if (! empty ( $_POST ) || ! empty ( $_GET )) {
            $keys = explode ( ",", $strParam );
            if (count ( $keys ) > 1) {
                $set_var = true;
            }
            foreach ( $keys as $key => $item ) {
                if (isset ( $_POST [$item] )) {
                    $var [$item] = ($_POST [$item]);
                }
                if (isset ( $_GET [$item] )) {
                    $var [$item] = ($_GET [$item]);
                }
            }
            if ($set_var) {
                $value = $var;
            }
            else {
                $value = isset ( $_GET [$strParam] ) ? ($_GET [$strParam]) : (isset ( $_POST [$strParam] ) ? ($_POST [$strParam]) : "");
            }
        }
        else {
            global $$strParam;
            $value = $$strParam;
        }
        return is_array ( $value ) ? $value : trim ( $value );
    }
    
    /**
     * 提示信息
     */
    public static function redirect($msg, $url) {
        echo "<script language=javascript>alert(".$msg.");parent.main.location.href=".$url.";</script>";
    }
}
?>
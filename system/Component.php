<?php
/**
 * $Id: component.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 * @desc page组件
 */
class Component {
    
    /**
     * 组件渲染函数
     * @param unknown $page
     * @param string $params
     * @throws Exception
     */
    public static function display($page, $params = null){
        $pathInfo = str_replace('_', '/', $page);
        $cfg = BPF::getInstance()->getConfig('Config_Common');
        $pagePath = 'view/'.strtolower($pathInfo).$cfg['tpl_suffix'];
        if(file_exists($pagePath)){
            if ($params){
                self::setParams($params);
            }
            include($pagePath);
        }
        else {
            echo $pagePath.' not found!';
            throw new Exception($pagePath.' not found!', 1002);
        }
    }
    
    public static function setParams($value){
        self::$params = $value;
    }
    
    public static function getParams(){
        return self::$params;
    }
    
    public static $params;
    
    /**
     * @param String $var   要查找的变量
     * @param Array  $scope 要搜寻的范围
     * @param String        变量名称
     */
    public static function getVarName($var, $scope=null){
        $scope = $scope==null? $GLOBALS : $scope;
        $tmp = $var;
        $var = 'tmp_value_'.mt_rand();
        $name = array_search($var, $scope, true); // 根据值查找变量名称
        $var = $tmp;
        return $name;
    }
}

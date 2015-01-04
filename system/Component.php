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
            throw new Exception(
                $page.Const_ErrorMapping::getErrMsgByCode(Const_ErrorMapping::ERR_SYS_CLASS_NOT_FOUND),
                Const_ErrorMapping::ERR_SYS_CLASS_NOT_FOUND
            );
        }
    }
    
    public static function setParams($value){
        self::$params = $value;
    }
    
    public static function getParams(){
        return self::$params;
    }
    
    public static $params;
    
}

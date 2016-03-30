<?php

/**
 * $Id: component.php Jul 2, 2014 wangguoxing (554952580@qq.com) $
 * @desc page组件
 */
class Component {

    public static $params;

    /**
     * 组件渲染函数
     * @param string $page
     * @param string $params
     * @throws Exception
     */
    public static function display($page, $params = null) {
        $pathInfo = str_replace('_', '/', $page);
        $cfg = BPF::getInstance()->getConfig('Config_Common');
        $pagePath = 'view/' . strtolower($pathInfo) . $cfg['tpl_suffix'];
        if (file_exists($pagePath)) {
            if ($params) {
                self::setParams($params);
            }
            include($pagePath);
        } else {
            echo $pagePath . ' not found!';
            throw new Exception(
                $page . Const_CodeMessage::getMsgByCode(Const_CodeMessage::ERR_SYS_CLASS_NOT_FOUND),
                Const_CodeMessage::ERR_SYS_CLASS_NOT_FOUND
            );
        }
    }

    public static function getParams() {
        return self::$params;
    }

    public static function setParams($value) {
        self::$params = $value;
    }

}

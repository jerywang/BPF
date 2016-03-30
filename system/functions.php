<?php
/**
 * $Id: functions.php Jul 2, 2014 wangguoxing (554952580@qq.com) $
 */

/**
 * @param $className
 *
 * @return boolean
 *
 * @throws Exception
 */
function __autoload($className) {
    $class = $className;
    $pathArr = explode('_', $className);
    $className = array_pop($pathArr);
    $pathInfo = implode('/', $pathArr);
    $pathInfo = strtolower($pathInfo);
    if ($pathInfo) {
        $classPath = '/' . $pathInfo . '/' . $className . '.php';
    } else {
        $classPath = $pathInfo . '/' . $className . '.php';
    }
    $paths = autoLoadPath();
    foreach ($paths as $path) {
        if (file_exists($path . $classPath)) {
            require_once($path . $classPath);
            return true;
        }
    }
    throw new Exception(
        $class . ' ' .Const_CodeMessage::getMsgByCode(Const_CodeMessage::ERR_SYS_CLASS_NOT_FOUND),
        Const_CodeMessage::ERR_SYS_CLASS_NOT_FOUND
    );
}

/**
 * @return array
 */
function autoLoadPath() {
    return array(
        APP_PATH,
        APP_PATH . 'model',
        APP_PATH . 'library',
        ROOT_PATH . 'system',
    );
}
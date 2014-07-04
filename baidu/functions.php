<?php
/**
 * $Id: functions.php Jul 2, 2014 wangguoxing (wangguoxing@baidu.com) $
 */
function __autoload($className){
    $pathArr = explode('_', $className);
    $className = array_pop($pathArr);
    $pathInfo = implode('/', $pathArr);
    $pathInfo = strtolower($pathInfo);
    if ($pathInfo) {
    	$classPath = '/'.$pathInfo.'/'.$className.'.php';
    }
    else {
        $classPath = $pathInfo.'/'.$className.'.php';
    }
    $paths = autoLoadPath();
    foreach ($paths as $path) {
        if(file_exists($path.$classPath)){
            require_once($path.$classPath);
            return true;
        }
    }
    echo $path.$classPath.' not found!';
    throw new Exception($path.$classPath.' not found!', 1000);
}

function autoLoadPath(){
    return array(
       APP_PATH,
       APP_PATH.'model',
	   APP_PATH.'library',
       ROOT_PATH.'core',
       ROOT_PATH.'baidu',
    );
}

?>

<?php
/**
 * $Id: index.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);//开发环境
//error_reporting(~E_ALL);//生产环境
$time = -microtime(true);
$memory = -memory_get_usage();


define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('APP_PATH', dirname(__FILE__).'/');
define('SYS_PATH', ROOT_PATH.'system/');
define('CORE_PATH', ROOT_PATH.'core/');

require SYS_PATH.'functions.php';
BPF::getInstance()->run();


$time += microtime(true);
$memory += memory_get_usage();
echo '
<div style="margin-top:50px;padding:10px;border-top:solid 1px #ccc">
<div>此次运行消耗内存：'.($memory/1024).'K</div>
<div>此次运行消耗时间：'.($time*1000).'ms</div>
</div>
';
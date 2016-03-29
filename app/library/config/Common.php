<?php

/**
 * class App_Config_Common
 * @author wangguoxing
 */
class Config_Common {

    public static $config = array(
        'base_url' => 'http://dev.jerry.com/',
        'cdn_url' => 'http://cdn.jerry.com/',
        'include_url' => 'http://include.jerry.com/',
        'css_version' => '20140622',
        'js_version' => '20140622',
        'tpl_suffix' => '.phtml',
        'allow_debug_ip' => array('/^192\.168/', '/^127/'),
    );

}
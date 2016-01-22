<?php

/**
 * $Id: Route.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 */
class Config_Route {

    public static $config = array(
        'Controller_App_Home_Index' =>
            array(
                '^/demo$',
                '^/$'
            ),
        'Controller_App_Sample_Sample' =>
            array(
                '^/sample$',
                '^/sample/(.+)$',
                //'^/sample/(.*)$',
                '^/jerry/$',
            ),
    );
}
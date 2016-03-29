<?php

/**
 * $Id: Route.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 */
class Config_Route {

    public static $config = array(
        'Controller_App_Home_Index' =>
            array(
                "/^\/$/",
                "/index$/",
            ),
        'Controller_App_Sample_Sample' =>
            array(
                "/sample$/",
                "/sample\/[a-z]+/",
//                "/sample\/(.*)$/",
            ),
    );
}
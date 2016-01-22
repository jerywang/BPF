<?php

class Config_Varnish {

    public static $config = array(
        // 首页
        'Controller_App_Home_Index' => array(
            'smaxage' => 60,
            'maxage' => 60
        ),
        'Controller_App_Home_Sample' => array(
            'smaxage' => 60,
            'maxage' => 60
        ),
    );
}

//$this->response->set_header("Expires", gmdate("D, d M Y H:i:s", 0) . " GMT");


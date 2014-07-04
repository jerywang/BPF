<?php
class Config_Cache {
    
    public static $config = array (
            'memcache' => array (
                    array ('127.0.0.1', 11211, 33 ),
                    array ('127.0.0.1', 11211, 34 ),
            ),
            'redis' => array (
                    array ('127.0.0.1', 6379),
                    array ('127.0.0.1', 6379),
            ),
    );
}

//$this->response->set_header("Expires", gmdate("D, d M Y H:i:s", 0) . " GMT");


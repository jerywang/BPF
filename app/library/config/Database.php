<?php

/**
 * Class Config_Database 数据库配置
 * 支持一主多从集群, slave故障自动剔除
 */
class Config_Database {
    
    const default_fetch_mode = PDO::FETCH_ASSOC;
    
    public static $config = array(
        //bpf集群
        'bpf' => array(
            'username' => 'root',
            'password' => '123456',
            'charset'  => 'utf8',
            'dbname'   => 'test',
            'init_attributes' => array(),
            'init_statements' => array(),
            'default_fetch_mode' => self::default_fetch_mode,
            'master' => array (
                'host' => '127.0.0.1',
                'port' => 3306,
            ),
            'slave' => array (
                array(
                    'host' => '127.0.0.1',
                    'port' => 3306,
                ),
                array(
                    'host' => '127.0.0.1',
                    'port' => 3306,
                ),
            ),
        ),

        //trade集群
        'trade' => array(
            'username' => 'root',
            'password' => '123456',
            'charset'  => 'utf8',
            'dbname'   => 'test',
            'init_attributes' => array(),
            'init_statements' => array(),
            'default_fetch_mode' => self::default_fetch_mode,
            'master' => array (
                'host' => '127.0.0.1',
                'port' => 3306,
            ),
            'slave' => array (
                array(
                    //'dsn'=>'mysql:host=127.0.0.1;port=3306;dbname=test',
                    'host' => '127.0.0.1',
                    'port' => 3306,
                ),
                array(
                    //'dsn'=>'mysql:host=127.0.0.1;port=3306;dbname=test',
                    'host' => '127.0.0.1',
                    'port' => 3306,
                ),
            ),
        )
    );
}
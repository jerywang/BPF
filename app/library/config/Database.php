<?php
class Config_Database {
    
    const default_fetch_mode = PDO::FETCH_ASSOC;
    
    public static $config = array(
        'master' => array (
            'dsn'=>'mysql:host=127.0.0.1;dbname=test',
            'username' => 'root',
            'password' => '123456',
            'init_attributes' => array(),
            'init_statements' => array('SET NAMES utf8'),
            'default_fetch_mode' => self::default_fetch_mode
        ),
    
        'slave' => array (
            'dsn'=>'mysql:host=127.0.0.1;dbname=test',
            'username' => 'root',
            'password' => '123456',
            'init_attributes' => array(),
            'init_statements' => array('SET NAMES utf8'),
            'default_fetch_mode' => self::default_fetch_mode
        ),
        
        'nhmaster' => array (
            'dsn'=>'mysql:host=192.168.1.24;dbname=test_db',
            'username' => 'test_db',
            'password' => '123456',
            'init_attributes' => array(),
            'init_statements' => array('SET NAMES utf8'),
            'default_fetch_mode' => self::default_fetch_mode
        ),
        'nhslave' => array (
            'dsn'=>'mysql:host=192.168.1.24;dbname=test_db',
            'username' => 'readonly',
            'password' => '123456',
            'init_attributes' => array(),
            'init_statements' => array('SET NAMES utf8'),
            'default_fetch_mode' => self::default_fetch_mode
        ),
    );
}
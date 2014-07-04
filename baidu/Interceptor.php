<?php
/**
 * $Id: Interceptor.php Jul 4, 2014 wangguoxing (wangguoxing@baidu.com) $
 */
abstract class Interceptor {
    abstract public function before();
    abstract public function after();
}

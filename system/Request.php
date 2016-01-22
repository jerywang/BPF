<?php

/**
 * $Id: Request.php 2014-6-30 wangguoxing (wangguoxing@system.com) $
 */
class Request {

    public $matches;

    public function setRouterMatches($matches) {
        $this->matches = $matches;
    }

    public function getRouterMatches() {
        return $this->matches;
    }

    public function getClientIp() {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "Unknow";
        }
        return $ip;
    }
}
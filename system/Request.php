<?php

/**
 * $Id: Request.php 2014-6-30 wangguoxing (554952580@qq.com) $
 */
class Request {

    private $matches = null;

    private $pathInfo = null;

    private $userInfo = null;

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        if(isset($this->$name)) {
            return($this->$name);
        } else {
            return null;
        }
    }

    public function setUserInfo($userInfo = null) {
        if($userInfo != null) {
            $this->userInfo = $userInfo;
        }
    }

    public function getUserInfo() {
        return $this->userInfo;
    }

    public function setRouterMatches($matches) {
        $this->matches = $matches;
    }

    public function getRouterMatches() {
        return $this->matches;
    }

    public function setPathInfo($path) {
        $this->pathInfo = $path;
    }

    public function getPathInfo() {
        return $this->pathInfo;
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
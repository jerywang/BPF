<?php

/**
 * $Id Base.php 2015-01-04 11:14 wangguoxing $
 * Desc:
 */
abstract class Controller_App_Base extends Controller {

    public function execute() {
        try {
            $res = $this->call();
            Log::notice('request: ' . json_encode(array_merge($_GET, $_POST))
                . ', response: ' . json_encode(BPF::getInstance()->getResponse()->data));
            return $res;
        } catch (Exception $e) {
            $errMsg = $e->getMessage();
            list($sysMsg, $apiMsg) = explode('|', $errMsg);
            Log::warning(sprintf("[msg]execute %s failed code[%s] error[%s] file[%s] line[%s]",
                $this->getClass(), $e->getCode(), $sysMsg, $e->getFile(), $e->getLine()));
//            throw new Exception($e->getMessage(), $e->getCode());
        }
        return null;
    }

    abstract public function call();

    /**
     * @return string
     */
    protected function getClass() {
        return __CLASS__;
    }

}
<?php

/**
 * $Id Base.php 2015-01-04 11:14 wangguoxing $
 * Desc:
 */
abstract class Controller_App_Base extends Controller {

    public function execute() {
        try {
            return $this->call();
        } catch (Exception $e) {
            $errmsg = $e->getMessage();
            list($sysMsg, $apiMsg) = explode('|', $errmsg);
            Logger::warning(sprintf("[msg]execute %s failed code[%s] error[%s] file[%s] line[%s] time [%s]",
                $this->getClass(), $e->getCode(), $sysMsg, $e->getFile(), $e->getLine(), date('H:i:s')), $e->getCode());
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    abstract public function call();

    /**
     * @return string
     */
    protected function getClass() {
        return __CLASS__;
    }

}
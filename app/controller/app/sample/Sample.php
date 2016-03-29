<?php

class Controller_App_Sample_Sample extends Controller_App_Base {
    public function call() {
        BPF::getInstance()->getResponse()->isJson(true);
        $this->setAttr("id", 1111);
    }
}
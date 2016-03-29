<?php

class Controller_App_Sample_Sample extends Controller_App_Base {
    public function call() {
        print_r(BPF::getInstance()->getRequest()->getRouterMatches());
        BPF::getInstance()->getResponse()->isJson(true);
        $this->setAttr("id", 1111);
    }
}
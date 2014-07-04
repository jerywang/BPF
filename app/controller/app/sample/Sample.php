<?php
class Controller_App_Sample_Sample extends Controller {
    public function execute(){
        BPF::getInstance()->getResponse()->isAjax(true);
        $this->setAttr("id",1111);
    }
}
<?php

class JavaStatement_astore_0 extends JavaStatement {

    public function execute () {
    
        $this->setLocalstorage(0, $this->getStack());

    }

}   

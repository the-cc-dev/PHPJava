<?php

class JavaStatement_if_icmpge extends JavaStatement {

    public function execute () {
    

        $offset = $this->getByteCodeStream()->readShort();

        $leftOperand = $this->getStack();
        $rightOperand = $this->getStack();

        if ($leftOperand <= $rightOperand) {

            $this->getByteCodeStream()->setOffset($this->getPointer() + $offset);

        }

    }

}   

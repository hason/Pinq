<?php

namespace Pinq\Iterators;

class WhereInIterator extends OperationIterator
{
    public function valid()
    {
        while(parent::valid()) {
            if($this->OtherValues->Contains(parent::current())) {
                return true;
            }
            
            parent::next();
        }
        return false;
    }
}
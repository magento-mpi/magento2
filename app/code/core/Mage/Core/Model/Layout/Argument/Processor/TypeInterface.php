<?php

interface Mage_Core_Model_Layout_Argument_Processor_TypeInterface
{
    /**
     * Process argument value
     * @param $value
     * @return mixed
     */
    public function process($value);
}

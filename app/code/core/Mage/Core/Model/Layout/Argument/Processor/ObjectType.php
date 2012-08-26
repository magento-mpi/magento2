<?php

class Mage_Core_Model_Layout_Argument_Processor_ObjectType
    extends Mage_Core_Model_Layout_Argument_Processor_TypeAbstract
    implements Mage_Core_Model_Layout_Argument_Processor_TypeInterface
{
    /**
     * Instantiate model object
     * @param string $value
     * @return false|Mage_Core_Model_Abstract
     */
    public function process($value)
    {
        $valueInstance = $this->_objectFactory->getModelInstance($value);
        return $valueInstance;
    }
}

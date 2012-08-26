<?php

abstract class Mage_Core_Model_Layout_Argument_Processor_TypeAbstract
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * Common constructor of argument types
     * @param array $args
     * @throws InvalidArgumentException
     */
    public function __construct(array $args = array())
    {
        if (!isset($args['objectFactory'])) {
            throw new InvalidArgumentException('Not all required parameters were passed');
        }
        $this->_objectFactory = $args['objectFactory'];
        if (false === ($this->_objectFactory instanceof Mage_Core_Model_Config)) {
            throw new InvalidArgumentException('Passed wrong instance of object factory');
        }
    }
}

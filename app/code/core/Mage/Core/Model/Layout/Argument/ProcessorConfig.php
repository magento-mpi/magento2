<?php

class Mage_Core_Model_Layout_Argument_ProcessorConfig
{
    const LAYOUT_ARGUMENT_TYPE_OBJECT = 'object';

    /**
     * Array of argument handlers
     * @var array
     */
    protected $_argumentHandlers = array();

    public function __construct()
    {
        $this->_argumentHandlers = array(
            self::LAYOUT_ARGUMENT_TYPE_OBJECT => 'Mage_Core_Model_Layout_Argument_Processor_ObjectType'
        );
    }

    /**
     * Get argument handler class name by given type
     * @param string $type
     * @return string
     * @throws InvalidArgumentException
     */
    public function getArgumentHandlerByType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('Passed invalid type of argument');
        }
        if (!isset($this->_argumentHandlers[$type])) {
            throw new InvalidArgumentException('Argument type ' . $type . ' is not exists');
        }
        return $this->_argumentHandlers[$type];
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout config processor
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
        if (false == is_string($type)) {
            throw new InvalidArgumentException('Passed invalid type of argument');
        }

        if (!isset($this->_argumentHandlers[$type])) {
            throw new InvalidArgumentException('Argument type ' . $type . ' is not exists');
        }

        return $this->_argumentHandlers[$type];
    }
}

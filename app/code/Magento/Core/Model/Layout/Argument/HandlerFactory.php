<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout config processor
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_HandlerFactory
{
    const LAYOUT_ARGUMENT_TYPE_OBJECT = 'object';
    const LAYOUT_ARGUMENT_TYPE_OPTIONS = 'options';
    const LAYOUT_ARGUMENT_TYPE_URL = 'url';
    const LAYOUT_ARGUMENT_TYPE_ARRAY = 'array';
    const LAYOUT_ARGUMENT_TYPE_BOOLEAN = 'boolean';
    const LAYOUT_ARGUMENT_TYPE_HELPER = 'helper';
    const LAYOUT_ARGUMENT_TYPE_NUMBER = 'number';
    const LAYOUT_ARGUMENT_TYPE_STRING = 'string';

    /**
     * Array of argument handler factories
     * @var array
     */
    protected $_handlerFactories = array(
        self::LAYOUT_ARGUMENT_TYPE_OBJECT => 'Magento_Core_Model_Layout_Argument_Handler_Object',
        self::LAYOUT_ARGUMENT_TYPE_OPTIONS => 'Magento_Core_Model_Layout_Argument_Handler_Options',
        self::LAYOUT_ARGUMENT_TYPE_URL => 'Magento_Core_Model_Layout_Argument_Handler_Url',
        self::LAYOUT_ARGUMENT_TYPE_ARRAY => 'Magento_Core_Model_Layout_Argument_Handler_Array',
        self::LAYOUT_ARGUMENT_TYPE_BOOLEAN => 'Magento_Core_Model_Layout_Argument_Handler_Boolean',
        self::LAYOUT_ARGUMENT_TYPE_HELPER => 'Magento_Core_Model_Layout_Argument_Handler_Helper',
        self::LAYOUT_ARGUMENT_TYPE_NUMBER => 'Magento_Core_Model_Layout_Argument_Handler_Number',
        self::LAYOUT_ARGUMENT_TYPE_STRING => 'Magento_Core_Model_Layout_Argument_Handler_String',
    );

    /**
     * Argument handlers list
     *
     * @var array
     */
    protected $_argumentHandlers = array();

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get argument handler factory by given type
     * @param string $type
     * @return Magento_Core_Model_Layout_Argument_HandlerInterface
     * @throws InvalidArgumentException
     */
    public function getArgumentHandlerByType($type)
    {
        if (false == is_string($type)) {
            throw new InvalidArgumentException('Passed invalid argument handler type');
        }

        if (!isset($this->_handlerFactories[$type])) {
            throw new InvalidArgumentException(__('Argument handler %1 does not exist', $type));
        }

        if (isset($this->_argumentHandlers[$type])) {
            return $this->_argumentHandlers[$type];
        }
        /** @var $handler Magento_Core_Model_Layout_Argument_HandlerInterface */
        $handler = $this->_objectManager->create($this->_handlerFactories[$type], array());

        if (false === ($handler instanceof Magento_Core_Model_Layout_Argument_HandlerInterface)) {
            throw new InvalidArgumentException(
                __('%1 type handler must implement Magento_Core_Model_Layout_Argument_HandlerInterface', $type)
            );
        }

        $this->_argumentHandlers[$type] = $handler;
        return $handler;
    }
}

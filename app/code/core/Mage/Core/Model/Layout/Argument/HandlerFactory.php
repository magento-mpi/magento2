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
class Mage_Core_Model_Layout_Argument_HandlerFactory
{
    const LAYOUT_ARGUMENT_TYPE_OBJECT  = 'object';
    const LAYOUT_ARGUMENT_TYPE_OPTIONS = 'options';
    const LAYOUT_ARGUMENT_TYPE_URL     = 'url';

    /**
     * Array of argument handler factories
     * @var array
     */
    protected $_handlerFactories = array(
        self::LAYOUT_ARGUMENT_TYPE_OBJECT  => 'Mage_Core_Model_Layout_Argument_Handler_Object',
        self::LAYOUT_ARGUMENT_TYPE_OPTIONS => 'Mage_Core_Model_Layout_Argument_Handler_Options',
        self::LAYOUT_ARGUMENT_TYPE_URL     => 'Mage_Core_Model_Layout_Argument_Handler_Url'
    );

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
     * @return Mage_Core_Model_Layout_Argument_HandlerInterface
     * @throws InvalidArgumentException
     */
    public function getArgumentHandlerByType($type)
    {
        if (false == is_string($type)) {
            throw new InvalidArgumentException('Passed invalid argument handler type');
        }

        if (!isset($this->_handlerFactories[$type])) {
            throw new InvalidArgumentException('Argument handler ' . $type . ' is not exists');
        }

        return $this->_objectManager->create($this->_handlerFactories[$type], array(), false);
    }
}

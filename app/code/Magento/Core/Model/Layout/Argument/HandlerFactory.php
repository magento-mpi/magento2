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
namespace Magento\Core\Model\Layout\Argument;

class HandlerFactory
{
    const LAYOUT_ARGUMENT_TYPE_OBJECT  = 'object';
    const LAYOUT_ARGUMENT_TYPE_OPTIONS = 'options';
    const LAYOUT_ARGUMENT_TYPE_URL     = 'url';

    /**
     * Array of argument handler factories
     * @var array
     */
    protected $_handlerFactories = array(
        self::LAYOUT_ARGUMENT_TYPE_OBJECT  => '\Magento\Core\Model\Layout\Argument\Handler\Object',
        self::LAYOUT_ARGUMENT_TYPE_OPTIONS => '\Magento\Core\Model\Layout\Argument\Handler\Options',
        self::LAYOUT_ARGUMENT_TYPE_URL     => '\Magento\Core\Model\Layout\Argument\Handler\Url'
    );

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get argument handler factory by given type
     * @param string $type
     * @return \Magento\Core\Model\Layout\Argument\HandlerInterface
     * @throws \InvalidArgumentException
     */
    public function getArgumentHandlerByType($type)
    {
        if (false == is_string($type)) {
            throw new \InvalidArgumentException('Passed invalid argument handler type');
        }

        if (!isset($this->_handlerFactories[$type])) {
            throw new \InvalidArgumentException('Argument handler ' . $type . ' is not exists');
        }

        return $this->_objectManager->create($this->_handlerFactories[$type], array());
    }
}

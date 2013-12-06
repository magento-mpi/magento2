<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout config processor
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\View\Layout\Argument;

class HandlerFactory
{
    /**
     * Array of argument handler factories
     * @var array
     */
    protected $_handlerFactories = array();

    /**
     * Argument handlers list
     *
     * @var array
     */
    protected $_argumentHandlers = array();

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array $handlerFactories
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        array $handlerFactories = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_handlerFactories = $handlerFactories;
    }

    /**
     * Get argument handler factory by given type
     * @param string $type
     * @return \Magento\View\Layout\Argument\HandlerInterface
     * @throws \InvalidArgumentException
     */
    public function getArgumentHandlerByType($type)
    {
        if (false == is_string($type)) {
            throw new \InvalidArgumentException('Passed invalid argument handler type');
        }

        if (!isset($this->_handlerFactories[$type])) {
            throw new \InvalidArgumentException("Argument handler {$type} does not exist");
        }

        if (isset($this->_argumentHandlers[$type])) {
            return $this->_argumentHandlers[$type];
        }
        /** @var $handler \Magento\View\Layout\Argument\HandlerInterface */
        $handler = $this->_objectManager->create($this->_handlerFactories[$type], array());

        if (false === ($handler instanceof \Magento\View\Layout\Argument\HandlerInterface)) {
            throw new \InvalidArgumentException(
                "{$type} type handler must implement \\Magento\\View\\Layout\\Argument\\HandlerInterface"
            );
        }

        $this->_argumentHandlers[$type] = $handler;
        return $handler;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->_handlerFactories);
    }
}

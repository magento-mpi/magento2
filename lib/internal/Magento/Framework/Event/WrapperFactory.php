<?php
/**
 * Observer model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Event;

class WrapperFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create wrapper instance
     *
     * @param array $arguments
     * @return \Magento\Framework\Event\Observer
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Framework\Event\Observer', $arguments);
    }
}

<?php
/**
 * Observer model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Event;

class WrapperFactory
{
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
     * Create wrapper instance
     *
     * @param array $arguments
     * @return \Magento\Event\Observer
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Event\Observer', $arguments);
    }
}

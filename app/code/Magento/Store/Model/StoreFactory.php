<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Model;

class StoreFactory
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
     * Create store instance
     *
     * @param array $arguments
     * @return Store
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Store\Model\Store', $arguments);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_StoreFactory
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
     * @return Magento_Core_Model_Store
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Core_Model_Store', $arguments);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_StoreFactory
{
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
     * Create store instance
     *
     * @param array $arguments
     * @return Mage_Core_Model_Store
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Store', $arguments);
    }
}

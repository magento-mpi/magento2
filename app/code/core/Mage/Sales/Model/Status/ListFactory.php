<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Status_ListFactory
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
     * Create status list instance
     *
     * @param array $arguments
     * @return Mage_Sales_Model_Status_List
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Sales_Model_Status_List', $arguments);
    }
}

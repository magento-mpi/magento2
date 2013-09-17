<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Status_ListFactory
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
     * @return Magento_Sales_Model_Status_List
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Sales_Model_Status_List', $arguments);
    }
}

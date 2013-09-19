<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for \Magento_Sales_Model_Order_Pdf_Items_Abstract
 */
class Magento_Sales_Model_Order_Pdf_ItemsFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento_ObjectManager $objectManager
     */
    public function __construct(\Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function get($instanceName, array $data = array())
    {
        return $this->_objectManager->get($instanceName, $data);
    }
}

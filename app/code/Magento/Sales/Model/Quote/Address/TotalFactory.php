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
 * Factory class for Magento_Sales_Model_Quote_Address_Total_Abstract
 */
class Magento_Sales_Model_Quote_Address_TotalFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Quote address factory constructor
     *
     * @param Magento_ObjectManager $objManager
     */
    public function __construct(Magento_ObjectManager $objManager)
    {
        $this->_objectManager = $objManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    public function create($instanceName, array $data = array())
    {
        return $this->_objectManager->create($instanceName, $data);
    }
}

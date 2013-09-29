<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Checkout Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Resource_Setup extends Magento_Eav_Model_Entity_Setup
{
    /**
     * @var Magento_Customer_Helper_Address
     */
    protected $_customerAddress;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory
     * @param Magento_Customer_Helper_Address $customerAddress
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        Magento_Customer_Helper_Address $customerAddress,
        $resourceName,
        $moduleName = 'Magento_Checkout',
        $connectionName = ''
    ) {
        $this->_customerAddress = $customerAddress;
        parent::__construct($context, $cache, $attrGrCollFactory, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return Magento_Customer_Helper_Address
     */
    public function getCustomerAddress()
    {
        return $this->_customerAddress;
    }
}

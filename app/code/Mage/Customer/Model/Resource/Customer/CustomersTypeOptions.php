<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterpise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event statuses option array
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Customer_CustomersTypeOptions implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Customer_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Customer_Helper_Data $customertHelper
     */
    public function __construct(Mage_Customer_Helper_Data $customerHelper)
    {
        $this->_helper = $customerHelper;
    }

    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER  => $this->_helper->__('Customer'),
            Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR => $this->_helper->__('Visitor'),
        );
    }
}

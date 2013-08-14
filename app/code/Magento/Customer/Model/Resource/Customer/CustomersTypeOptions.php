<?php
/**
 * Customer type option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Customer_Model_Resource_Customer_CustomersTypeOptions implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Customer_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Customer_Helper_Data $customerHelper
     */
    public function __construct(Magento_Customer_Helper_Data $customerHelper)
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
            Magento_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER  => $this->_helper->__('Customer'),
            Magento_Log_Model_Visitor::VISITOR_TYPE_VISITOR => $this->_helper->__('Visitor'),
        );
    }
}

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
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER  => __('Customer'),
            Magento_Log_Model_Visitor::VISITOR_TYPE_VISITOR => __('Visitor'),
        );
    }
}

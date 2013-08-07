<?php
/**
 * Customer type option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Model_Resource_Customer_CustomersTypeOptions implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER  => __('Customer'),
            Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR => __('Visitor'),
        );
    }
}

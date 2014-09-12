<?php
/**
 * Customer type option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\Customer;

class CustomersTypeOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\Customer\Model\Visitor::VISITOR_TYPE_CUSTOMER => __('Customer'),
            \Magento\Customer\Model\Visitor::VISITOR_TYPE_VISITOR => __('Visitor')
        );
    }
}

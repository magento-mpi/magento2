<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model of customer address types
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Config\Source\Address;

class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\Customer\Model\Address\AbstractAddress::TYPE_BILLING => __('Billing Address'),
            \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING => __('Shipping Address')
        );
    }
}

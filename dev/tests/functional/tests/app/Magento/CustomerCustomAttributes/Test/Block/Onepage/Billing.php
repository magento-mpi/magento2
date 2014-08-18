<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Block\Onepage;

use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class Billing
 * One page checkout billing block on frontend
 */
class Billing extends \Magento\Checkout\Test\Block\Onepage\Billing
{
    /**
     * Locator for customer attribute on Checkout page
     *
     * @var string
     */
    protected $customerAttribute = "[name='billing[%s]']";

    /**
     * Check if Customer custom Attribute visible
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return bool
     */
    public function isCustomerAttributeVisible(CustomerCustomAttribute $customerAttribute)
    {
        return $this->_rootElement->find(
            sprintf($this->customerAttribute, $customerAttribute->getAttributeCode())
        )->isVisible();
    }
}

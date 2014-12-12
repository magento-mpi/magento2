<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Block\Form;

use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class CustomerForm
 * Customer account edit form
 */
class CustomerForm extends \Magento\Customer\Test\Block\Form\CustomerForm
{
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

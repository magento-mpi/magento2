<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class CustomerForm
 * Customer account edit form
 */
class CustomerForm extends Form
{
    /**
     * Save button button css selector
     *
     * @var string
     */
    protected $saveButton = '[type="submit"]';

    /**
     * Locator for customer attribute on Edit Account Information page
     *
     * @var string
     */
    protected $customerAttribute = "[name='%s[]']";

    /**
     * Click on save button
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }

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

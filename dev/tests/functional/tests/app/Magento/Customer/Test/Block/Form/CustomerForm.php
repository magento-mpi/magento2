<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;

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
     * @param string $attributeCode
     * @return bool
     */
    public function isCustomerAttributeVisible($attributeCode)
    {
        $selector = "[name='$attributeCode\\[\\]']";
        return $this->_rootElement->find($selector)->isVisible();
    }
}

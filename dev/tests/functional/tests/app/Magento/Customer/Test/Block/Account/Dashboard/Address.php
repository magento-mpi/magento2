<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Account\Dashboard;

use Mtf\Block\Block;

/**
 * Customer Dashboard Address Book block
 *
 * @package Magento\Customer\Test\Block\Dashboard
 */
class Address extends Block
{
    /**
     *  Default Billing Address Edit link
     *
     * @var string
     */
    protected $defaultBillingAddressEdit = '[data-ui-id=default-billing-edit-link]';

    /**
     * Edit Default Billing Address
     */
    public function editBillingAddress()
    {
        $this->_rootElement->find($this->defaultBillingAddressEdit)->click();
        $this->waitForElementNotVisible($this->defaultBillingAddressEdit);
    }
}

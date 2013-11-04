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

namespace Magento\Customer\Test\Block\Dashboard;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Customer Dashboard Address Book block
 *
 * @package Magento\Customer\Test\Block\Dashboard
 */
class AddressBook extends Block
{
    /**
     *  Default Billing Address Edit link
     *
     * @var string
     */
    private $defaultBillingAddressEdit;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        //Elements
        $this->defaultBillingAddressEdit = '[data-ui-id=default-billing-edit-link]';
    }

    /**
     * Edit Default Billing Address
     */
    public function editBillingAddress()
    {
        $this->_rootElement->find($this->defaultBillingAddressEdit)->click();
    }
}

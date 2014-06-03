<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Test\Block\Account;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Addresses default block
 *
 */
class AddressesDefault extends Block
{
    /**
     * Selector for change billing address
     *
     * @var string
     */
    protected $changeBillingAddressSelector = '.box.address.billing .subtitle a';

    /**
     * Click on address book menu item
     */
    public function goToAddressBook()
    {
        $this->waitForElementVisible($this->changeBillingAddressSelector, Locator::SELECTOR_CSS);
        $this->_rootElement->find($this->changeBillingAddressSelector, Locator::SELECTOR_CSS)->click();
    }
}

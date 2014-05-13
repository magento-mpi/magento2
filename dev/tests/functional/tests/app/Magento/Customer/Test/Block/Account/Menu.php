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
 * Menu block
 *
 */
class Menu extends Block
{
    /**
     * Address book link selector
     *
     * @var string
     */
    protected $addressBook = '//li/a[text()[normalize-space()="Address Book"]]';

    /**
     * Click on address book menu item
     */
    public function goToAddressBook()
    {
        $this->_rootElement->find($this->addressBook, Locator::SELECTOR_XPATH)->click();
    }
}

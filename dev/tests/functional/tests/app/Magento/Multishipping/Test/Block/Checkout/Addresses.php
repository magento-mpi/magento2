<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Addresses
 * Multishipping checkout choose item addresses block
 *
 */
class Addresses extends Block
{
    /**
     * 'Enter New Address' button
     *
     * @var string
     */
    protected $newAddress = '[data-role="add-new-address"]';

    /**
     * 'Continue to Shipping Information' button
     *
     * @var string
     */
    protected $continue = '[class*=continue][data-role="can-continue"]';

    /**
     * Add new customer address
     */
    public function addNewAddress()
    {
        $this->_rootElement->find($this->newAddress, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Select shipping addresses for products
     *
     * @param GuestPaypalDirect $fixture
     */
    public function selectAddresses(GuestPaypalDirect $fixture)
    {
        $products = $fixture->getBindings();
        foreach ($products as $key => $value) {
            $this->_rootElement->find(
                '//tr[//a[text()="' . $key . '"]]/following-sibling::*//select',
                Locator::SELECTOR_XPATH,
                'select'
            )->setValue($value);
        }
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Mustishipping checkout shipping.
 */
class Shipping extends Block
{
    /**
     * 'Continue' button.
     *
     * @var string
     */
    protected $continueButton = '.action.continue';

    /**
     * Select shipping methods.
     *
     * @param GuestPaypalDirect $fixture
     * @return void
     */
    public function selectShippingMethod(GuestPaypalDirect $fixture)
    {
        /** @var $fixture \Magento\Checkout\Test\Fixture\Checkout */
        $shippingMethods = $fixture->getShippingMethods();
        $count = 1;
        foreach ($shippingMethods as $shipping) {
            $method = $shipping->getData('fields');
            $selector = '//div[' . $count++ . '][contains(@class,"block-shipping")]//dt[text()="'
                . $method['shipping_service'] . '"]/following-sibling::*//*[contains(text(), "'
                . $method['shipping_method'] . '")]';
            $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        }
        $this->_rootElement->find($this->continueButton)->click();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;

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

<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;

/**
 * Class Shipping
 * Mustishipping checkout shipping
 *
 * @package Magento\Multishipping\Test\Block\Checkout
 */
class Shipping extends Block
{
    /**
     * Select shipping methods
     *
     * @param GuestPaypalDirect $fixture
     */
    public function selectShippingMethod(GuestPaypalDirect $fixture)
    {
        /** @var $fixture \Magento\Checkout\Test\Fixture\Checkout */
        $shippingMethods = $fixture->getShippingMethods();
        $count = 2;
        foreach ($shippingMethods as $shipping) {
            $method = $shipping->getData('fields');
            $selector = '//div[' . $count++ . '][@class="block shipping"]//dt[text()="'
                . $method['shipping_service'] . '"]/following-sibling::*//*[contains(text(), "'
                . $method['shipping_method'] . '")]';
            $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        }
        $this->_rootElement->find('//button[@class="action continue"]', Locator::SELECTOR_XPATH)->click();
    }
}

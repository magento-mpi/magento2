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

namespace Magento\Checkout\Test\Block\Multishipping;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Shipping
 * Mustishipping checkout shipping
 *
 * @package Magento\Checkout\Test\Block\Multishipping
 */
class Shipping extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    private $continue;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->continue = '.buttons-set button';
    }

    /**
     * Select shipping methods
     *
     * @param Checkout $fixture
     */
    public function selectShippingMethod(Checkout $fixture)
    {
        /** @var $fixture \Magento\Checkout\Test\Fixture\Checkout */
        $shippingMethods = $fixture->getShippingMethods();
        $count = 1;
        foreach ($shippingMethods as $shipping) {
            $method = $shipping->getData('fields');
            $selector = '//div[' . $count++ . '][@class="col2-set"]//dt[text()="'
                . $method['shipping_service'] . '"]/following-sibling::*//*[contains(text(), "'
                . $method['shipping_method'] . '")]';
            $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        }
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
    }
}

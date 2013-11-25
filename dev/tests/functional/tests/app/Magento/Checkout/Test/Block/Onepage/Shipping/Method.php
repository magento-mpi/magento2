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

namespace Magento\Checkout\Test\Block\Onepage\Shipping;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Method
 * One page checkout status shipping method block
 *
 * @package Magento\Checkout\Test\Block\Onepage\Shipping
 */
class Method extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#shipping-method-buttons-container button';

    /**
     * Select shipping method
     *
     * @param Checkout $fixture
     */
    public function selectShippingMethod(Checkout $fixture)
    {
        $shippingMethod = $fixture->getShippingMethods()->getData('fields');
        $selector = '//dt[text()="' . $shippingMethod['shipping_service']
            . '"]/following-sibling::*//*[contains(text(), "' . $shippingMethod['shipping_method'] . '")]';
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('#shipping-method-please-wait');
    }
}

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
     * Shipping method selector
     *
     * @var string
     */
    protected $shippingMethod = '//dt[text()="%s"]/following-sibling::*//*[contains(text(), "%s")]';

    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#shipping-method-buttons-container button';

    /**
     * Select shipping method
     *
     * @param Checkout|\Magento\Shipping\Test\Fixture\Method[] $fixture
     */
    public function selectShippingMethod($fixture)
    {
        if ($fixture instanceof \Magento\Shipping\Test\Fixture\Method) {
            $shippingMethod = $fixture->getData('fields');
        } else {
            $shippingMethod = $fixture->getShippingMethods()->getData('fields');
        }
        $selector = sprintf(
            $this->shippingMethod, $shippingMethod['shipping_service'], $shippingMethod['shipping_method']
        );
        $this->waitForElementVisible($selector, Locator::SELECTOR_XPATH);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('#shipping-method-please-wait');
    }
}

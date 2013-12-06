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

namespace Magento\Checkout\Test\Block\Cart;

use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\TestCase\Functional;

/**
 * Class Shipping
 * Shopping cart estimate shipping and tax block
 *
 * @package Magento\Checkout\Test\Block\Cart
 */
class Shipping extends Block
{
    /**
     * Assert that the passed in shipping method is present
     *
     * @param \Magento\Shipping\Test\Fixture\Method $fixture
     * @return bool
     */
    public function assertShippingCarrierMethod($fixture)
    {
        $shippingMethod = $fixture->getData('fields');

        $selector = '//span[text()="'
            . $shippingMethod['shipping_service']
            . '"]/following::*/div[@class="field choice item"]//*[contains(text(), "'
            . $shippingMethod['shipping_method']
            . '")]';

        return Functional::assertTrue($this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible());
    }

    /**
     * Click on 'Estimate Shipping and Tax' to expand destination prompts.
     */
    public function clickEstimateShippingTax()
    {
        $this->_rootElement->click();
    }
    /**
     * Press 'Get A Quote' button
     */
    public function clickGetAQuote()
    {
        $this->_rootElement->find('//button[@class="action quote"]', Locator::SELECTOR_XPATH)->click();
        $this->waitForElementNotVisible('.please-wait');
    }

    /**
     * Fill destination address for shipping estimate
     *
     * @param Checkout $fixture
     */
    public function fillDestination(Checkout $fixture)
    {
        $destination = $fixture->getBillingAddress();
        if (!$destination) {
            return;
        }
        $this->_rootElement->find('//select[@id="country"]', Locator::SELECTOR_XPATH, 'select')
            ->setValue($destination->getCountry());
        $this->_rootElement->find('//select[@id="region_id"]', Locator::SELECTOR_XPATH, 'select')
            ->setValue($destination->getRegion());
        $cityElement = $this->_rootElement->find('//input[@id="city"]', Locator::SELECTOR_XPATH);
        if ($cityElement->isVisible()) {
            $cityElement->setValue($destination->getCity());
        }
        $this->_rootElement->find('//input[@id="postcode"]', Locator::SELECTOR_XPATH)
            ->setValue($destination->getPostCode());
    }
}

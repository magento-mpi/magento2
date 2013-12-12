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
use Magento\Shipping\Test\Fixture\Method;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Shipping
 * Shopping cart estimate shipping and tax block
 *
 * @package Magento\Checkout\Test\Block\Cart
 */
class Shipping extends Block
{
    /**
     * Selector to access the destination city element
     *
     * @var string
     */
    protected $citySelector = '//input[@id="city"]';

    /**
     * Selector to access the destination country element
     *
     * @var string
     */
    protected $countrySelector = '//select[@id="country"]';

    /**
     * Selector to access the 'Get A Quote' button element
     *
     * @var string
     */
    protected $getAQuoteButtonSelector = '//button[@class="action quote"]';

    /**
     * Selector to access the destination postal/zip code element
     *
     * @var string
     */
    protected $postalCodeSelector = '//input[@id="postcode"]';

    /**
     * Selector to access the destination region element
     *
     * @var string
     */
    protected $regionSelector = '//input[@id="region"]';

    /**
     * Selector to access the destination region_id element
     *
     * @var string
     */
    protected $regionIdSelector = '//select[@id="region_id"]';

    /**
     * Selector to access the specific shipping carrier method
     *
     * @var string
     */
    protected $shippingCarrierMethodSelector =
        '//span[text()="%s"]/following::*/div[@class="field choice item"]//*[contains(text(), "%s")]';

    /**
     * Assert that the passed in shipping method is present
     *
     * @param string $carrier
     * @param string $method
     * @return bool
     */
    public function isShippingCarrierMethodVisible($carrier, $method)
    {
        $selector = sprintf($this->shippingCarrierMethodSelector, $carrier, $method);
        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
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
        $this->_rootElement->find($this->getAQuoteButtonSelector, Locator::SELECTOR_XPATH)->click();
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
        $this->_rootElement->find($this->countrySelector, Locator::SELECTOR_XPATH, 'select')
            ->setValue($destination->getCountry());
        // Region is either a SELECT or an INPUT depending on the country
        $regionElement = $this->_rootElement->find($this->regionIdSelector, Locator::SELECTOR_XPATH, 'select');
        if (!$regionElement->isVisible()) {
            $regionElement = $this->_rootElement->find($this->regionSelector, Locator::SELECTOR_XPATH);
        }
        $regionElement->setValue($destination->getRegion());
        $cityElement = $this->_rootElement->find($this->citySelector, Locator::SELECTOR_XPATH);
        if ($cityElement->isVisible()) {
            $cityElement->setValue($destination->getCity());
        }
        $this->_rootElement->find($this->postalCodeSelector, Locator::SELECTOR_XPATH)
            ->setValue($destination->getPostCode());
    }
}

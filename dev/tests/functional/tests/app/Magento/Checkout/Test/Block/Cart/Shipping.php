<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Shipping
 * Cart shipping block
 */
class Shipping extends Form
{
    /**
     * Form wrapper selector
     *
     * @var string
     */
    protected $formWrapper = '.content';

    /**
     * Open shipping form selector
     *
     * @var string
     */
    protected $openForm = '.title';

    /**
     * Get quote selector
     *
     * @var string
     */
    protected $getQuote = '.action.quote';

    /**
     * Update total selector
     *
     * @var string
     */
    protected $updateTotalSelector = '.action.update';

    /**
     * Selector to access the shipping carrier method
     *
     * @var string
     */
    protected $shippingCarrierMethodSelector = '//span[text()="%s"]';

    /**
     * Selector to access the shipping price
     *
     * @var string
     */
    protected $shippingPriceSelector = '/following::*/div[@class="field choice item"]//*[contains(text(), "%s")]';

    /**
     * From with shipping available shipping methods
     *
     * @var string
     */
    protected $shippingMethodForm = '#co-shipping-method-form';

    /**
     * Open estimate shipping and tax form
     *
     * @return void
     */
    public function openEstimateShippingAndTax()
    {
        if (!$this->_rootElement->find($this->formWrapper)->isVisible()) {
            $this->_rootElement->find($this->openForm)->click();
        }
    }

    /**
     * Click Get quote button
     *
     * @return void
     */
    public function clickGetQuote()
    {
        $this->_rootElement->find($this->getQuote)->click();
    }

    /**
     * Select shipping method
     *
     * @param $shipping
     * @return void
     */
    public function selectShippingMethod($shipping)
    {
        $shippingSelector = $this->shippingCarrierMethodSelector . $this->shippingPriceSelector;
        $shippingMethod = $this->_rootElement->find(
            sprintf($shippingSelector, $shipping['carrier'], $shipping['method']),
            Locator::SELECTOR_XPATH
        );
        if ($shippingMethod->isVisible()) {
            $shippingMethod->click();
            $this->_rootElement->find($this->updateTotalSelector, Locator::SELECTOR_CSS)->click();
        }
    }

    /**
     * Determines if the specified shipping carrier/method is visible on the cart
     *
     * @param $carrier
     * @param $method
     * @return bool
     */
    public function isShippingCarrierMethodVisible($carrier, $method)
    {
        $shippingMethodForm = $this->_rootElement->find($this->shippingMethodForm);
        $this->_rootElement->waitUntil(
            function () use ($shippingMethodForm) {
                return $shippingMethodForm->isVisible() ? true : null;
            }
        );
        $shippingSelector = $this->shippingCarrierMethodSelector . $this->shippingPriceSelector;
        return $this->_rootElement->find(
            sprintf($shippingSelector, $carrier, $method),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }
}

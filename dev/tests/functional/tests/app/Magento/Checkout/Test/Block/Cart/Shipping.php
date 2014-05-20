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
 *
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
    protected $shippingCarrierMethodSelector =
        '//span[text()="%s"]/following::*/div[@class="field choice item"]//*[contains(text(), "%s")]';

    /**
     * From with shipping available shipping methods
     *
     * @var string
     */
    protected $shippingMethodForm = '#co-shipping-method-form';

    /**
     * Open estimate shipping and tax form
     */
    public function openEstimateShippingAndTax()
    {
        if (!$this->_rootElement->find($this->formWrapper)->isVisible()) {
            $this->_rootElement->find($this->openForm)->click();
        }
    }

    /**
     * Get quote
     */
    public function getQuote()
    {
        $this->_rootElement->find($this->getQuote)->click();
    }

    /**
     * Select shipping method
     *
     * @param $shipping
     */
    public function selectShippingMethod($shipping)
    {
        $selector = sprintf($this->shippingCarrierMethodSelector, $shipping['carrier'], $shipping['method']);
        if(!$this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible()){
            $this->openEstimateShippingAndTax();
        }
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->_rootElement->find($this->updateTotalSelector, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Fill shipping and tax form
     *
     * @param $address
     */
    public function fillShippingAddress($address)
    {
        $this->openEstimateShippingAndTax();
        $this->fill($address);
        $this->getQuote();
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
        $selector = sprintf($this->shippingCarrierMethodSelector, $carrier, $method);
        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }
}

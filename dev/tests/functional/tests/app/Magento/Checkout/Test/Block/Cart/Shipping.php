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

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Cart shipping block
 *
 * @package Magento\Checkout\Test\Block\Cart
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

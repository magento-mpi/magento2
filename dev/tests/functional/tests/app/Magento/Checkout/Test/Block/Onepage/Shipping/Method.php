<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage\Shipping;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Method
 * One page checkout status shipping method block
 *
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
     * Wait element
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Add gift options
     *
     * @var string
     */
    protected $allowGiftOptions = 'input[name="allow_gift_options"]';

    /**
     * Gift Options for individual items
     *
     * @var string
     */
    protected $allowGiftOptionsForItems = 'input[name="allow_gift_options_for_items"]';

    /**
     * Gift Wrapping Design
     *
     * @var string
     */
    protected $giftWrapping = 'select[name$="[design]"]';

    /**
     * Select shipping method
     *
     * @param array $method
     * @return void
     */
    public function selectShippingMethod(array $method)
    {
        $selector = sprintf($this->shippingMethod, $method['shipping_service'], $method['shipping_method']);
        $this->waitForElementVisible($selector, Locator::SELECTOR_XPATH);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Click continue button
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }

    /**
     * Check if Gift Wrapping Design Available on Onepage Checkout
     *
     * @param string $giftWrappingDesign
     * @return bool
     */
    public function isGiftWrappingAvailable($giftWrappingDesign)
    {
        $this->_rootElement->find($this->allowGiftOptions)->click();
        $this->_rootElement->find($this->allowGiftOptionsForItems)->click();
        $giftWrappings = $this->_rootElement->find($this->giftWrapping)->getText();
        return strpos($giftWrappings, $giftWrappingDesign);
    }
}

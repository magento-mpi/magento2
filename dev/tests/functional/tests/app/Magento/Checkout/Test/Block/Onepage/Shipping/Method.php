<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
    protected $shippingMethod = '//dt[text()="%s"]/following-sibling::*//label/span[contains(text(), "%s")]';

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
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Method
 * Adminhtml sales order create shipping method block
 */
class Method extends Block
{
    /**
     * 'Get shipping methods and rates' link
     *
     * @var string
     */
    protected $shippingMethodsLink = '#order-shipping-method-summary a';

    /**
     * Shipping method
     *
     * @var string
     */
    protected $shippingMethod = '//dt[contains(.,"%s")]/following-sibling::*//*[contains(text(), "%s")]';

    /**
     * Select shipping method
     *
     * @param array $shippingMethod
     */
    public function selectShippingMethod(array $shippingMethod)
    {
        $this->_rootElement->click();
        $this->_rootElement->find($this->shippingMethodsLink)->click();
        $selector = sprintf(
            $this->shippingMethod,
            $shippingMethod['shipping_service'],
            $shippingMethod['shipping_method']
        );
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
    }
}

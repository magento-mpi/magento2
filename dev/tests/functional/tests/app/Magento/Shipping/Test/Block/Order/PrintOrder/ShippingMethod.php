<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Order\PrintOrder;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class ShippingMethod
 * Shipping Method block on order's print page.
 */
class ShippingMethod extends Block
{
    /**
     * Shipping method selector.
     *
     * @var string
     */
    protected $shippingMethodSelector = './.[contains(., "%s")]';

    /**
     * Check if shipping method is visible in print order page.
     *
     * @param string $shippingMethod
     * @return bool
     */
    public function isShippingMethodVisible($shippingMethod)
    {
        return $this->_rootElement->find(
            sprintf($this->shippingMethodSelector, $shippingMethod),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }
}

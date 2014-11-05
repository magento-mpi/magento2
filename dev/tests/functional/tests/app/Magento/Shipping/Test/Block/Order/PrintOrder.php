<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Order;

/**
 * Class PrintOrder
 * Print Order block
 */
class PrintOrder extends \Magento\Sales\Test\Block\Order\PrintOrder
{
    /**
     * Shipping method selector.
     *
     * @var string
     */
    protected $shippingMethodSelector = '.shipping.method';

    /**
     * Returns shipping method block on print order page.
     *
     * @return \Magento\Shipping\Test\Block\Order\PrintOrder\ShippingMethod
     */
    public function getShippingMethodBlock()
    {
        $shippingMethodBlock = $this->blockFactory->create(
            'Magento\Shipping\Test\Block\Order\PrintOrder\ShippingMethod',
            ['element' => $this->_rootElement->find($this->shippingMethodSelector)]
        );

        return $shippingMethodBlock;
    }
}

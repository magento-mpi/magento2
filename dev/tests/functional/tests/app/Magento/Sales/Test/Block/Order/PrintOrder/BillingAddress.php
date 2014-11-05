<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order\PrintOrder;

use Mtf\Block\Block;

/**
 * Class BillingAddress
 * Billing Address block on order's print page
 */
class BillingAddress extends Block
{
    /**
     * Address selector.
     *
     * @var string
     */
    protected $addressSelector = 'address';

    /**
     * Returns billing address.
     *
     * @return string
     */
    public function getBillingAddress()
    {
        return $this->_rootElement->find($this->addressSelector)->getText();
    }
}

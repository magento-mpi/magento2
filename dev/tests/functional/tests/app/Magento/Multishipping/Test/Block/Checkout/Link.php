<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Block;

/**
 * Class Link
 * Multishipping cart link
 *
 */
class Link extends Block
{
    /**
     * Press 'Proceed to Checkout' link
     */
    public function multipleAddressesCheckout()
    {
        $this->_rootElement->click();
    }
}

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

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Block;

/**
 * Class Link
 * Multishipping cart link
 *
 * @package Magento\Multishipping\Test\Block\Checkout
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

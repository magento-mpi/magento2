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

namespace Magento\Checkout\Test\Block\Multishipping;

use Mtf\Block\Block;

/**
 * Class Link
 * Multishipping cart link
 *
 * @package Magento\Checkout\Test\Block\Multishipping
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

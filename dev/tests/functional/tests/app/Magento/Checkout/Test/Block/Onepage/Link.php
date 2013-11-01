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

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Block;

/**
 * Class Link
 * One page checkout cart link
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Link extends Block
{
    /**
     * Press 'Proceed to Checkout' link
     */
    public function proceedToCheckout()
    {
        $this->_rootElement->click();
    }
}

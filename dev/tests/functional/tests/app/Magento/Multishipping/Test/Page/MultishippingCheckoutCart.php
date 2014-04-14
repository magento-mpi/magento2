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

namespace Magento\Multishipping\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * class MultishippingCheckoutCart
 *
 * @package Magento\Multishipping\Test\Page
 */
class MultishippingCheckoutCart extends Page
{
    /**
     * URL for multishipping checkout cart page
     */
    const MCA = 'multishipping/checkout/cart';

    /**
     * Multishipping cart link block
     *
     * @var string
     */
    protected $multishippingLinkBlock = '.action.multicheckout';

    /**
     * Get multishipping cart link block
     *
     * @return \Magento\Multishipping\Test\Block\Checkout\Link
     */
    public function getMultishippingLinkBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutLink(
            $this->_browser->find($this->multishippingLinkBlock, Locator::SELECTOR_CSS)
        );
    }
}

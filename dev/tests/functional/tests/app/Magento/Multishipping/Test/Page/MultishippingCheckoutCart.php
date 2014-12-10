<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * class MultishippingCheckoutCart
 *
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

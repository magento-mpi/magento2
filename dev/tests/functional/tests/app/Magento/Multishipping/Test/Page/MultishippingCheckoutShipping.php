<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * class MultishippingCheckoutShipping
 * Select shipping methods page
 *
 */
class MultishippingCheckoutShipping extends Page
{
    /**
     * URL for shipping page
     */
    const MCA = 'multishipping/checkout/shipping';

    /**
     * Mustishipping checkout shipping
     *
     * @var string
     */
    protected $shippingBlock = '#shipping_method_form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get shipping block
     *
     * @return \Magento\Multishipping\Test\Block\Checkout\Shipping
     */
    public function getShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutShipping(
            $this->_browser->find($this->shippingBlock, Locator::SELECTOR_CSS)
        );
    }
}

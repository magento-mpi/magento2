<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * class MultishippingCheckoutAddresses
 * Multishipping addresses page
 *
 */
class MultishippingCheckoutAddresses extends Page
{
    /**
     * URL for multishipping addresss page
     */
    const MCA = 'multishipping/checkout/addresses';

    /**
     * Multishipping checkout choose item addresses block
     *
     * @var string
     */
    protected $addressesBlock = '#checkout_multishipping_form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get multishipping checkout choose item addresses block
     *
     * @return \Magento\Multishipping\Test\Block\Checkout\Addresses
     */
    public function getAddressesBlock()
    {
        return Factory::getBlockFactory()->getMagentoMultishippingCheckoutAddresses(
            $this->_browser->find($this->addressesBlock, Locator::SELECTOR_CSS)
        );
    }
}

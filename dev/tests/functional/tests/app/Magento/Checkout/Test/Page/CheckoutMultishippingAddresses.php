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

namespace Magento\Checkout\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Block\Multishipping;

/**
 * Class CheckoutMultishippingAddresses
 * Multishipping addresses page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingAddresses extends Page
{
    /**
     * URL for multishipping addresss page
     */
    const MCA = 'checkout/multishipping/addresses';

    /**
     * Multishipping checkout choose item addresses block
     *
     * @var Multishipping\Addresses
     * @private
     */
    private $addressesBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->addressesBlock = Factory::getBlockFactory()->getMagentoCheckoutMultishippingAddresses(
            $this->_browser->find('#checkout_multishipping_form', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get multishipping checkout choose item addresses block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Addresses
     */
    public function getAddressesBlock()
    {
        return $this->addressesBlock;
    }
}

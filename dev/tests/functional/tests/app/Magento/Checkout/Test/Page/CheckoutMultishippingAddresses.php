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
     * @return \Magento\Checkout\Test\Block\Multishipping\Addresses
     */
    public function getAddressesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutMultishippingAddresses(
            $this->_browser->find($this->addressesBlock, Locator::SELECTOR_CSS)
        );
    }
}

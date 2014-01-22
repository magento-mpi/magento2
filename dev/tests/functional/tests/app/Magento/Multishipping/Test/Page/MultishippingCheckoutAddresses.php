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
 * class MultishippingCheckoutAddresses
 * Multishipping addresses page
 *
 * @package Magento\Multishipping\Test\Page
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

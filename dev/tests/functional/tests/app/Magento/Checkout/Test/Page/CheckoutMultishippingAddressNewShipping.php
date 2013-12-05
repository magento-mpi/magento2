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
 * Class CheckoutMultishippingAddressNewShipping
 * Create Shipping Address page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingAddressNewShipping extends Page
{
    /**
     * URL for new shipping address page
     */
    const MCA = 'checkout/multishipping_address/newShipping';

    /**
     * Form for edit customer address
     *
     * @var string
     */
    protected $editBlock = '#form-validate';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get form for edit customer address
     *
     * @return \Magento\Customer\Test\Block\Address\Edit
     */
    public function getEditBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerAddressEdit(
            $this->_browser->find($this->editBlock, Locator::SELECTOR_CSS)
        );
    }
}

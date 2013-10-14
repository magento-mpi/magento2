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
use Magento\Customer\Test\Block\Address;

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
     * @var Address\Edit
     * @private
     */
    private $addressesEditBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->addressesEditBlock = Factory::getBlockFactory()->getMagentoCustomerAddressEdit(
            $this->_browser->find('#form-validate', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get form for edit customer address
     *
     * @return \Magento\Customer\Test\Block\Address\Edit
     */
    public function getAddressesEditBlock()
    {
        return $this->addressesEditBlock;
    }
}

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
 * Class CheckoutMultishippingRegister
 * Register new customer while performing multishipping addresses checkout
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingRegister extends Page
{
    /**
     * URL for register customer page
     */
    const MCA = 'checkout/multishipping/register';

    /**
     * Customer register block form
     *
     * @var string
     */
    protected $registerBlock = '#form-validate';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get customer register block form
     *
     * @return \Magento\Customer\Test\Block\Form\Register
     */
    public function getRegisterBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerFormRegister(
            $this->_browser->find($this->registerBlock, Locator::SELECTOR_CSS)
        );
    }
}

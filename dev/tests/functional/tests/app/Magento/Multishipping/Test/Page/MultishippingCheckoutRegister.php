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
 * class MultishippingCheckoutRegister
 * Register new customer while performing multishipping addresses checkout
 *
 * @package Magento\Multishipping\Test\Page
 */
class MultishippingCheckoutRegister extends Page
{
    /**
     * URL for register customer page
     */
    const MCA = 'multishipping/checkout/register';

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

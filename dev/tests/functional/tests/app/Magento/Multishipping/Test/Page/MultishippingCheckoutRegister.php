<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * class MultishippingCheckoutRegister
 * Register new customer while performing multishipping addresses checkout
 *
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

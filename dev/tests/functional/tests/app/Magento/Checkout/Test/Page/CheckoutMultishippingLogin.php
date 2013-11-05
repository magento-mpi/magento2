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
use Magento\Customer\Test\Block\Form;

/**
 * Class CheckoutMultishippingLogin
 * Multishipping login page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingLogin extends Page
{
    /**
     * URL for multishipping login page
     */
    const MCA = 'checkout/multishipping/login';

    /**
     * Form for customer login
     *
     * @var Form\Login
     * @private
     */
    private $loginBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->loginBlock = Factory::getBlockFactory()->getMagentoCustomerFormLogin(
            $this->_browser->find('#login-form', Locator::SELECTOR_CSS)
        );
    }

    /**
     * @return \Magento\Customer\Test\Block\Form\Login
     */
    public function getLoginBlock()
    {
        return $this->loginBlock;
    }
}

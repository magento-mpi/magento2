<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Block\Form\Login;

/**
 * Class CustomerAccountLogin
 * Customer frontend login page.
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerAccountLogin extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer/account/login';

    /**
     * Form for customer login
     *
     * @var Login
     */
    private $loginBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->loginBlock = Factory::getBlockFactory()->getMagentoCustomerFormLogin(
            $this->_browser->find('login-form', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get customer login form
     *
     * @return Login
     */
    public function getLoginBlock()
    {
        return $this->loginBlock;
    }
}

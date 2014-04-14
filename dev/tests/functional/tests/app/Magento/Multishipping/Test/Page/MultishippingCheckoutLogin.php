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
 * class MultishippingCheckoutLogin
 * Multishipping login page
 *
 * @package Magento\Multishipping\Test\Page
 */
class MultishippingCheckoutLogin extends Page
{
    /**
     * URL for multishipping login page
     */
    const MCA = 'multishipping/checkout/login';

    /**
     * Form for customer login
     *
     * @var string
     */
    protected $loginBlock = '.login.container';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get form for customer login
     *
     * @return \Magento\Customer\Test\Block\Form\Login
     */
    public function getLoginBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerFormLogin(
            $this->_browser->find($this->loginBlock, Locator::SELECTOR_CSS)
        );
    }
}

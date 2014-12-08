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
 * class MultishippingCheckoutLogin
 * Multishipping login page
 *
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

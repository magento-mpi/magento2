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
use Magento\Customer\Test\Block\Form;
use Magento\Customer\Test\Block\DashboardHeaderPanelTitle;

/**
 * Class Login.
 * Customer frontend login page.
 *
 * @package Magento\Customer\Test\Page
 */
class Login extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer/account/login';

    /**
     * Form for customer login
     *
     * @var Form\Login
     * @private
     */
    private $loginBlock;

    /**
     * Dashboard header panel title
     *
     * @var DashboardHeaderPanelTitle
     */
    private $_dashboardHeaderPanelTitle;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->loginBlock = Factory::getBlockFactory()->getMagentoCustomerFormLogin(
            $this->_browser->find('login-form', Locator::SELECTOR_ID));
        $this->_dashboardHeaderPanelTitle = Factory::getBlockFactory()->getMagentoCustomerDashboardHeaderPanelTitle(
            $this->_browser->find('//div[@class="page-title"]/h1[contains(text(), "My Dashboard")]',
                Locator::SELECTOR_XPATH));
    }

    /**
     * Get customer login form
     *
     * @return \Magento\Customer\Test\Block\Form\Login
     */
    public function getLoginBlock()
    {
        return $this->loginBlock;
    }

    /**
     * Get dashboard panel title
     *
     * @return \Magento\Customer\Test\Block\DashboardHeaderPanelTitle
     */
    public function getDashboardHeaderPanelTitle()
    {
        return $this->_dashboardHeaderPanelTitle;
    }
}

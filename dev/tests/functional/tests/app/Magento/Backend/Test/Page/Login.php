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

namespace Magento\Backend\Test\Page;

use Mtf\Factory\Factory;
use Mtf\Page\Page;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\LoginForm;
use Magento\Backend\Test\Block\HeaderPanel;

/**
 * Class Login
 * Login page for backend
 *
 * @package Magento\Backend\Test\Page
 */
class Login extends Page
{
    /**
     * URL part for backend authorization
     */
    const MCA = 'admin/auth/login';

    /**
     * Form for login
     *
     * @var LoginForm
     * @locator id:login-form
     * @injectable
     */
    protected $loginFormBlock;

    /**
     * Header panel of admin dashboard
     *
     * @var HeaderPanel
     * @locator css:.header-panel
     * @injectable
     */
    protected $headerPanelBlock;

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->headerPanelBlock = Factory::getBlockFactory()->getMagentoBackendHeaderPanel(
            $this->_browser->find('.header-panel', Locator::SELECTOR_CSS));
    }

    /**
     * Get the login form block
     *
     * @return LoginForm
     */
    public function getLoginBlockForm()
    {
        return Factory::getBlockFactory()->getMagentoBackendLoginForm(
            $this->_browser->find('login-form', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get the header panel block of admin dashboard
     *
     * @return HeaderPanel
     */
    public function getHeaderPanelBlock()
    {
        return $this->headerPanelBlock;
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );

    }
}

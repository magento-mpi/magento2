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
     * @var string
     */
    protected $loginFormBlock = 'login-form';

    /**
     * Header panel of admin dashboard
     *
     * @var string
     */
    protected $headerPanelBlock = '.header-panel';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messageBlock = '#messages .messages';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get backend admin login form block
     *
     * @return \Magento\Backend\Test\Block\LoginForm
     */
    public function getLoginBlockForm()
    {
        return Factory::getBlockFactory()->getMagentoBackendLoginForm(
            $this->_browser->find($this->loginFormBlock, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get the header panel block of admin dashboard
     *
     * @return \Magento\Backend\Test\Block\HeaderPanel
     */
    public function getHeaderPanelBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendHeaderPanel(
            $this->_browser->find($this->headerPanelBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messageBlock)
        );
    }
}

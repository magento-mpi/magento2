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

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class AdminAuthLogin
 * Login page for backend
 *
 * @package Magento\Backend\Test\Page
 */
class AdminAuthLogin extends Page
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
    protected $loginBlock = '#login-form';

    /**
     * Header panel of admin dashboard
     *
     * @var string
     */
    protected $headerBlock = '.header-panel';

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
     * Get the login form block
     *
     * @return \Magento\Backend\Test\Block\Admin\Login
     */
    public function getLoginBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendAdminLogin(
            $this->_browser->find($this->loginBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get the header panel block of admin dashboard
     *
     * @return \Magento\Backend\Test\Block\Page\Header
     */
    public function getHeaderBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageHeader(
            $this->_browser->find($this->headerBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messageBlock));
    }

    public function waitForHeaderBlock()
    {
        $browser = $this->_browser;
        $selector = $this->headerBlock;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $item = $browser->find($selector);
                return $item->isVisible() ? true : null;
            }
        );
    }
}

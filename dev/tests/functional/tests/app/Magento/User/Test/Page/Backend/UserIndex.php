<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Page\Backend;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class UserIndex
 * System ->Permissions -> All Users page
 *
 */
class UserIndex extends Page
{
    /**
     * URL part for admin user page
     */
    const MCA = 'admin/user/';

    /**
     * Admin User Grid on backend
     *
     * @var string
     */
    protected $userGridBlock = 'permissionsUserGrid';

    /**
     * Message Block on page
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Admin header Block
     *
     * @var string
     */
    protected $adminPanelHeaderBlock = 'page-header';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get User Grid block
     *
     * @return \Magento\User\Test\Block\Backend\UserGrid
     */
    public function getUserGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoUserBackendUserGrid(
            $this->_browser->find($this->userGridBlock, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messagesBlock));
    }

    /**
     * Get Admin Panel Header Block
     *
     * @return \Magento\Backend\Test\Block\Page\Header
     */
    public function getAdminPanelHeaderBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageHeader(
            $this->_browser->find($this->adminPanelHeaderBlock, Locator::SELECTOR_CLASS_NAME)
        );
    }
}


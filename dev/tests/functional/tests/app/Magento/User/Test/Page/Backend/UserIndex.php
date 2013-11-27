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
 * @package Magento\User\Test\Page\Backend
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
     * @var /UserGrid
     */
    protected $userGrid;

    /**
     * @var \Magento\Core\Test\Block\Messages
     */
    protected $messagesBlock;

    /**
     * @var \Magento\Backend\Test\Block\Page\Header
     */
    protected $adminPanelHeader;

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->adminPanelHeader = Factory::getBlockFactory()->getMagentoBackendPageHeader(
            $this->_browser->find('header-panel', Locator::SELECTOR_CLASS_NAME)
        );
        $this->userGrid = Factory::getBlockFactory()->getMagentoUserBackendUserGrid(
            $this->_browser->find('permissionsUserGrid', Locator::SELECTOR_ID)
        );
        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages'));
    }

    /**
     * @return /UserGrid
     */
    public function getUserGrid()
    {
        return $this->userGrid;
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->messagesBlock;
    }

    /**
     * @return \Magento\Backend\Test\Block\Page\Header
     */
    public function getAdminPanelHeader()
    {
        return $this->adminPanelHeader;
    }
}
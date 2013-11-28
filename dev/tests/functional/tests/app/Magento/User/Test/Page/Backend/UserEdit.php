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
use Magento\Core\Test\Block\Messages;

/**
 * Class UserEdit
 *
 * @package Magento\User\Test\Page\Backend
 */
class UserEdit extends Page
{
    /**
     * URL for new admin user
     */
    const MCA = 'admin/user/edit/user_id/';

    /**
     * Form for admin user creation
     *
     * @var string
     */
    protected  $editFormBlock = 'page:main-container';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Role Grid Block
     * var @string
     */
    protected $roleGridBlock = 'permissionsUserRolesGrid';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get form for admin user creation/edit
     *
     * @return \Magento\User\Test\Block\User\Edit\Form
     */
    public function getEditFormBlock()
    {
        return Factory::getBlockFactory()->getMagentoUserUserEditForm(
            $this->_browser->find($this->editFormBlock, Locator::SELECTOR_ID)
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
            $this->_browser->find($this->messagesBlock));
    }

    /**
     * Get Role grid
     *
     * @return \Magento\User\Test\Block\User\Edit\Tab\Roles
     */
    public function getRoleGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoUserUserEditTabRoles(
            $this->_browser->find($this->roleGridBlock, Locator::SELECTOR_ID));
    }
}


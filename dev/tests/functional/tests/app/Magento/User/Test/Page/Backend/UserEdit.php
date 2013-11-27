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
     * @var Form
     */
    private $editForm;

    /**
     * Global messages block
     *
     * @var Messages
     */
    private $messagesBlock;

    /**
     * Role grid on Edit User page
     */
    protected $roleGrid;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->editForm = Factory::getBlockFactory()->getMagentoUserUserEditForm(
            $this->_browser->find('[id="page:main-container"]', Locator::SELECTOR_CSS)
        );
        $this->roleGrid = Factory::getBlockFactory()->getMagentoUserUserEditTabRoles(
            $this->_browser->find('permissionsUserRolesGrid', Locator::SELECTOR_ID)
        );
        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * Get form for admin user creation/edit
     *
     * @return \Magento\User\Test\Block\User\Edit\Form
     */
    public function getEditForm()
    {
        return $this->editForm;
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
     * @return \Magento\User\Test\Block\User\Edit\Tab\Roles
     */
    public function getRoleGrid()
    {
        return $this->roleGrid;
    }
}

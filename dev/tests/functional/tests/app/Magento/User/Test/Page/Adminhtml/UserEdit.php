<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class UserEdit
 */
class UserEdit extends BackendPage
{
    const MCA = 'admin/user/edit';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'userForm' => [
            'name' => 'userForm',
            'class' => 'Magento\User\Test\Block\Adminhtml\User\UserForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'rolesGrid' => [
            'name' => 'rolesGrid',
            'class' => 'Magento\User\Test\Block\Adminhtml\User\Tab\Role\Grid',
            'locator' => '[id="permissionsUserRolesGrid"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\User\Test\Block\Adminhtml\User\UserForm
     */
    public function getUserForm()
    {
        return $this->getBlockInstance('userForm');
    }

    /**
     * @return \Magento\User\Test\Block\Adminhtml\User\Tab\Role\Grid
     */
    public function getRolesGrid()
    {
        return $this->getBlockInstance('rolesGrid');
    }
}

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
 * Class UserRoleEditRole
 */
class UserRoleEditRole extends BackendPage
{
    const MCA = 'admin/user_role/editrole';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActions' => [
            'class' => 'Magento\User\Test\Block\Adminhtml\Role\PageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'roleFormTabs' => [
            'class' => 'Magento\User\Test\Block\Adminhtml\Role\RoleForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\User\Test\Block\Adminhtml\Role\PageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\User\Test\Block\Adminhtml\Role\RoleForm
     */
    public function getRoleFormTabs()
    {
        return $this->getBlockInstance('roleFormTabs');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

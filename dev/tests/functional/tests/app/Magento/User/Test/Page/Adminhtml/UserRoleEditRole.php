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
 *
 * @package Magento\User\Test\Page\Adminhtml
 */
class UserRoleEditRole extends BackendPage
{
    const MCA = 'admin/user_role/editrole';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\User\Test\Block\Adminhtml\Role\PageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'roleFormTabs' => [
            'name' => 'roleFormTabs',
            'class' => 'Magento\User\Test\Block\Adminhtml\Role\Edit',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
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
     * @return \Magento\User\Test\Block\Adminhtml\Role\Edit
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

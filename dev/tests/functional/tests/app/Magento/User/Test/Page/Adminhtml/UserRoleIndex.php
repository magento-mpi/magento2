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
 * Class UserRoleIndex
 *
 * @package Magento\User\Test\Page\Adminhtml
 */
class UserRoleIndex extends BackendPage
{
    const MCA = 'admin/user_role/index';

    protected $_blocks = [
        'roleActions' => [
            'name' => 'roleActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'roleGrid' => [
            'name' => 'roleGrid',
            'class' => 'Magento\User\Test\Block\Adminhtml\RoleGrid',
            'locator' => '#roleGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getRoleActions()
    {
        return $this->getBlockInstance('roleActions');
    }

    /**
     * @return \Magento\User\Test\Block\Adminhtml\RoleGrid
     */
    public function getRoleGrid()
    {
        return $this->getBlockInstance('roleGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

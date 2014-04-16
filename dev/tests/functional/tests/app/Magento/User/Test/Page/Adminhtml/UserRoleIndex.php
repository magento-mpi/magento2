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
            'class' => 'Magento\User\Test\Block\Adminhtml\Block\Widget\Grid\ColumnSet',
            'locator' => '#roleGrid',
            'strategy' => 'css selector',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
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
     * @return \Magento\User\Test\Block\Adminhtml\Block\Widget\Grid\ColumnSet
     */
    public function getRoleGrid()
    {
        return $this->getBlockInstance('roleGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }
}

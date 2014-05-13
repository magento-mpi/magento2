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
 * Class UserIndex
 */
class UserIndex extends BackendPage
{
    const MCA = 'admin/user';

    protected $_blocks = [
        'userActions' => [
            'name' => 'userActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'userGrid' => [
            'name' => 'userGrid',
            'class' => 'Magento\User\Test\Block\Adminhtml\UserGrid',
            'locator' => '#permissionsUserGrid',
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
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getUserActions()
    {
        return $this->getBlockInstance('userActions');
    }

    /**
     * @return \Magento\User\Test\Block\Adminhtml\UserGrid
     */
    public function getUserGrid()
    {
        return $this->getBlockInstance('userGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

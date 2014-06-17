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
        'pageActions' => [
            'name' => 'pageActions',
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
        'accessDeniedBlock' => [
            'name' => 'accessDeniedBlock',
            'class' => 'Magento\Backend\Test\Block\Denied',
            'locator' => '#anchor-content',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
<<<<<<< HEAD
    public function getUserActions()
    {
        return $this->getBlockInstance('userActions');
=======
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
>>>>>>> remotes/origin/MTA-53
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

    /**
     * @return \Magento\Backend\Test\Block\Denied
     */
    public function getAccessDeniedBlock()
    {
        return $this->getBlockInstance('accessDeniedBlock');
    }
}

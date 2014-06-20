<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class SystemVariableIndex
 */
class SystemVariableIndex extends BackendPage
{
    const MCA = 'admin/system_variable/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'systemVariableGrid' => [
            'name' => 'systemVariableGrid',
            'class' => 'Magento\Core\Test\Block\Adminhtml\SystemVariable\Grid',
            'locator' => '#customVariablesGrid',
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
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Adminhtml\SystemVariable\Grid
     */
    public function getSystemVariableGrid()
    {
        return $this->getBlockInstance('systemVariableGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

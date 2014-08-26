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

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'gridPageActions' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'systemVariableGrid' => [
            'class' => 'Magento\Backend\Test\Block\System\Variable\Grid',
            'locator' => '#customVariablesGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
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
     * @return \Magento\Backend\Test\Block\System\Variable\Grid
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

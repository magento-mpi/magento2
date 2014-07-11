<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class TargetRuleIndex
 * Backend target rule index page
 */
class TargetRuleIndex extends BackendPage
{
    const MCA = 'admin/targetrule/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'targetRuleGrid' => [
            'name' => 'targetRuleGrid',
            'class' => 'Magento\TargetRule\Test\Block\Adminhtml\Grid',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\TargetRule\Test\Block\Adminhtml\Grid
     */
    public function getTargetRuleGrid()
    {
        return $this->getBlockInstance('targetRuleGrid');
    }
}

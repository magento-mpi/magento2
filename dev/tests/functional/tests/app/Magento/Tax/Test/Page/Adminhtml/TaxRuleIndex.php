<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class TaxRuleIndex
 */
class TaxRuleIndex extends BackendPage
{
    const MCA = 'tax/rule/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'taxRuleGrid' => [
            'name' => 'taxRuleGrid',
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rule\Grid',
            'locator' => '#taxRuleGrid',
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
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Grid
     */
    public function getTaxRuleGrid()
    {
        return $this->getBlockInstance('taxRuleGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

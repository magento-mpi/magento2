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
 * Class TaxRateIndex
 */
class TaxRateIndex extends BackendPage
{
    const MCA = 'tax/rate/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'taxRateGrid' => [
            'name' => 'taxRateGrid',
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rate\Grid',
            'locator' => '#tax_rate_grid',
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
     * @return \Magento\Tax\Test\Block\Adminhtml\Rate\Grid
     */
    public function getTaxRateGrid()
    {
        return $this->getBlockInstance('taxRateGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

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

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'gridPageActions' => [
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rate\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'taxRateGrid' => [
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rate\Grid',
            'locator' => '#tax_rate_grid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Tax\Test\Block\Adminhtml\Rate\GridPageActions
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

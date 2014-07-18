<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class ExchangeRateIndex
 */
class ExchangeRateIndex extends BackendPage
{
    const MCA = 'admin/reward_rate/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'exchangeRateGrid' => [
            'name' => 'exchangeRateGrid',
            'class' => 'Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Grid',
            'locator' => '[id="page:main-container"]',
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
     * @return \Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Grid
     */
    public function getExchangeRateGrid()
    {
        return $this->getBlockInstance('exchangeRateGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

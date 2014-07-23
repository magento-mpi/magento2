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
 * Class RewardRateIndex
 * Index page reward exchange rates
 */
class RewardRateIndex extends BackendPage
{
    const MCA = 'admin/reward_rate/index';

    protected $_blocks = [
        'gridRate' => [
            'name' => 'gridRate',
            'class' => 'Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Grid',
            'locator' => '#rewardRatesGrid',
            'strategy' => 'css selector',
        ],
        'gridActions' => [
            'name' => 'gridActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
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
     * @return \Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Grid
     */
    public function getGridRate()
    {
        return $this->getBlockInstance('gridRate');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridActions()
    {
        return $this->getBlockInstance('gridActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}

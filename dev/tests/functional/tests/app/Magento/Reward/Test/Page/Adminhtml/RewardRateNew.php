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
 * Class RewardRateNew
 * Page new reward exchange rate
 */
class RewardRateNew extends BackendPage
{
    const MCA = 'admin/reward_rate/new';

    protected $_blocks = [
        'rewardRateForm' => [
            'name' => 'rewardRateForm',
            'class' => 'Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Edit\RewardRateForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Edit\RewardRateForm
     */
    public function getRewardRateForm()
    {
        return $this->getBlockInstance('rewardRateForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }
}

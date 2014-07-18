<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class RewardCustomerInfo
 */
class RewardCustomerInfo extends FrontendPage
{
    const MCA = 'reward/customer/info';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
        'accountMenuBlock' => [
            'name' => 'accountMenuBlock',
            'class' => 'Magento\Customer\Test\Block\Account\Links',
            'locator' => '.nav.items',
            'strategy' => 'css selector',
        ],
        'rewardPointsBlock' => [
            'name' => 'rewardPointsBlock',
            'class' => 'Magento\Reward\Test\Block\Customer\RewardPoints',
            'locator' => '//*[@class="columns"]/div[div[contains(@class, "block reward")]]',
            'strategy' => 'xpath',
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
     * @return \Magento\Customer\Test\Block\Account\Links
     */
    public function getAccountMenuBlock()
    {
        return $this->getBlockInstance('accountMenuBlock');
    }

    /**
     * @return \Magento\Reward\Test\Block\Customer\RewardPoints
     */
    public function getRewardPointsBlock()
    {
        return $this->getBlockInstance('rewardPointsBlock');
    }
}

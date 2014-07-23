<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Customer\RewardPoints;

use Mtf\Block\Block;

/**
 * Class RewardPointsInformation
 * Reward points balance Information block
 */
class RewardPointsInformation extends Block
{
    /**
     * Selector for reward current exchange rates
     *
     * @var string
     */
    protected $rewardRatesSelector = '.reward.rates';

    /**
     * Selector for current reward points balance button
     *
     * @var string
     */
    protected $rewardPointsBalanceSelector = '.content > :first-child';

    /**
     * Get current reward exchange rates
     *
     * @return string
     */
    public function getRewardPointsRates()
    {
        return $this->_rootElement->find($this->rewardRatesSelector)->getText();
    }

    /**
     * Get current reward points balance
     *
     * @return string
     */
    public function getRewardPointsBalance()
    {
        return $this->_rootElement->find($this->rewardPointsBalanceSelector)->getText();
    }
}

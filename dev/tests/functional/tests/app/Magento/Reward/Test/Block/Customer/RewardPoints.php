<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Customer;

use Mtf\Block\Block;

/**
 * Class RewardPoints
 * Reward points block on reward customer info page
 */
class RewardPoints extends Block
{
    /**
     * Returns Reward points balance Information
     *
     * @return array|string
     */
    public function getBalanceInformation()
    {
        return $this->_rootElement->find('.reward.info')->getText();
    }
}

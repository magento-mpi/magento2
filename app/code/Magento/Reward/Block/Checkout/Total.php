<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Block_Checkout_Total extends Magento_Checkout_Block_Total_Default
{
    protected $_template = 'checkout/total.phtml';

    /**
     * Return url to remove reward points from totals calculation
     *
     * @return string
     */
    public function getRemoveRewardTotalUrl()
    {
        return $this->getUrl('magento_reward/cart/remove');
    }
}

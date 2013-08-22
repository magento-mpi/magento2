<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Block_Checkout_Total extends Magento_Checkout_Block_Total_Default
{
    protected $_template = 'checkout/total.phtml';

    /**
     * Return url to remove reward points from totals calculation
     *
     * @return string
     */
    public function getRemoveRewardTotalUrl()
    {
        return $this->getUrl('enterprise_reward/cart/remove');
    }
}

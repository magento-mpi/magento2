<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Block\Checkout;

class Total extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Totals calculation template when checkout using reward points
     *
     * @var string
     */
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Page;

use Magento\Checkout\Test\Page\CheckoutOnepage as AbstractCheckoutOnepage;

/**
 * Class CheckoutOnepage
 * Page of checkout onepage
 */
class CheckoutOnepage extends AbstractCheckoutOnepage
{
    const MCA = 'reward_checkout/onepage';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['rewardPointsBlock'] = [
            'name' => 'rewardPointsBlock',
            'class' => 'Magento\Reward\Test\Block\Checkout\Payment\Additional',
            'locator' => '#reward_placer',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\Reward\Test\Block\Checkout\Payment\Additional
     */
    public function getRewardPointsBlock()
    {
        return $this->getBlockInstance('rewardPointsBlock');
    }
}

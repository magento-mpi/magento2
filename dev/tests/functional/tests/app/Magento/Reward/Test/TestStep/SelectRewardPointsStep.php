<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestStep;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Mtf\TestStep\TestStepInterface;

/**
 * Class SelectRewardPointsStep
 * Select reward points on onepage checkout page
 */
class SelectRewardPointsStep implements TestStepInterface
{
    /**
     * Array with payment methods
     *
     * @var array
     */
    protected $payment;

    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $payment
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $payment)
    {
        $this->payment = $payment;
        $this->checkoutOnepage = $checkoutOnepage;
    }

    /**
     * Select reward points
     *
     * @return void
     */
    public function run()
    {
        if ($this->payment['use_reward_points'] !== '-') {
            $this->checkoutOnepage->getRewardPointsBlock()->fillReward($this->payment);
        }
    }
}

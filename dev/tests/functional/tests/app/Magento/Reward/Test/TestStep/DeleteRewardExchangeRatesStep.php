<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestStep;

use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Mtf\TestStep\TestStepInterface;

/**
 * Class DeleteRewardExchangeRatesStep
 */
class DeleteRewardExchangeRatesStep implements TestStepInterface
{
    /**
     * Reward Exchange Rate grid page
     *
     * @var RewardRateIndex
     */
    protected $rewardRateIndexPage;

    /**
     * Reward Exchange Rate edit page
     *
     * @var RewardRateNew
     */
    protected $rewardRateNewPage;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param RewardRateIndex $rewardRateIndex
     * @param RewardRateNew $rewardRateNew
     */
    public function __construct(RewardRateIndex $rewardRateIndex, RewardRateNew $rewardRateNew)
    {
        $this->rewardRateIndexPage = $rewardRateIndex;
        $this->rewardRateNewPage = $rewardRateNew;
    }

    /**
     * Run step that delete reward exchange rate
     *
     * @return void
     */
    public function run()
    {
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
    }
}

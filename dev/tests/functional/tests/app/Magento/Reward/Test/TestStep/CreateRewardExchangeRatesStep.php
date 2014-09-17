<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestStep;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateRewardExchangeRatesStep
 * Create reward exchange rates
 */
class CreateRewardExchangeRatesStep implements TestStepInterface
{
    /**
     * Factory for Fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Array with reward rates dataSet names
     *
     * @var array
     */
    protected $rewardRates;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $rewardRates
     */
    public function __construct(FixtureFactory $fixtureFactory, array $rewardRates)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->rewardRates = $rewardRates;
    }

    /**
     * Create reward exchange rates
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->rewardRates as $rewardRate) {
            $exchangeRate = $this->fixtureFactory->createByCode('rewardRate', ['dataSet' => $rewardRate]);
            $exchangeRate->persist();
        }
    }
}

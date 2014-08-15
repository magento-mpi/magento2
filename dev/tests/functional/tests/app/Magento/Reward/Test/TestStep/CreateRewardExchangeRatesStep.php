<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestStep;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateRewardExchangeRatesStep
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
     * Factory for Step
     *
     * @var TestStepFactory
     */
    protected $testStepFactory;

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
     * @param TestStepFactory $testStepFactory
     * @param array $rewardRates
     */
    public function __construct(FixtureFactory $fixtureFactory, TestStepFactory $testStepFactory, array $rewardRates)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->testStepFactory = $testStepFactory;
        $this->rewardRates = $rewardRates;
    }

    /**
     * Create reward exchange rates
     *
     * @return void
     */
    public function run()
    {
        // Ensure that "Reward exchange rates" hadn't been created before
        $deleteExchangeRates = $this->testStepFactory->create(
            'Magento\Reward\Test\TestStep\DeleteRewardExchangeRatesStep'
        );
        $deleteExchangeRates->run();

        foreach ($this->rewardRates as $rewardRate) {
            $exchangeRate = $this->fixtureFactory->createByCode('rewardRate', ['dataSet' => $rewardRate]);
            $exchangeRate->persist();
        }
    }
}

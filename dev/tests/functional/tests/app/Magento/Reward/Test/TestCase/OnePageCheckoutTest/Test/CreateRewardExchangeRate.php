<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestCase\OnePageCheckoutTest\Test;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Step\StepFactory;
use Mtf\TestCase\Step\StepInterface;

class CreateRewardExchangeRate implements StepInterface
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
     * @var StepFactory
     */
    protected $stepFactory;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param StepFactory $stepFactory
     */
    public function __construct(FixtureFactory $fixtureFactory, StepFactory $stepFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->stepFactory = $stepFactory;
    }

    /**
     * Run step that creates reward exchange rate
     *
     * @return void
     */
    public function run()
    {
        $deleteExchangeRates = $this->stepFactory->create(
            'Magento\Reward\Test\TestCase\OnePageCheckoutTest\Test\DeleteRewardExchangeRate'
        );
        $deleteExchangeRates->run();

        $ratePointsToCurrency = $this->fixtureFactory->createByCode(
            'exchangeRate',
            ['dataSet' => 'rate_points_to_currency']
        );
        $ratePointsToCurrency->persist();
        $rateCurrencyToPoints = $this->fixtureFactory->createByCode(
            'exchangeRate',
            ['dataSet' => 'rate_currency_to_points']
        );
        $rateCurrencyToPoints->persist();
    }
}

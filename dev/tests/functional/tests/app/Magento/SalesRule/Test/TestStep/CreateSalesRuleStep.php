<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\TestStep;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateSalesRuleStep
 * Creating sales rule
 */
class CreateSalesRuleStep implements TestStepInterface
{
    /**
     * Sales Rule coupon
     *
     * @var string
     */
    protected $salesRule;

    /**
     * Factory for Fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $salesRule
     */
    public function __construct(FixtureFactory $fixtureFactory, $salesRule)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->salesRule = $salesRule;
    }

    /**
     * Create sales rule
     *
     * @return array
     */
    public function run()
    {
        $result['salesRule'] = null;
        if ($this->salesRule != '-') {
            $salesRule = $this->fixtureFactory->createByCode(
                'salesRuleInjectable',
                ['dataSet' => $this->salesRule]
            );
            $salesRule->persist();
            $result['salesRule'] = $salesRule;
        }

        return $result;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Fixture\Reminder;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;

/**
 * Source sales rule.
 */
class SalesruleId implements FixtureInterface
{
    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Resource data.
     *
     * @var string
     */
    protected $data;

    /**
     * Fixture sales rule.
     *
     * @var SalesRuleInjectable
     */
    protected $salesRule;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data = []
    ) {
        $this->params = $params;
        $this->salesRule = $fixtureFactory->createByCode('salesRuleInjectable', $data);

        if (!$this->salesRule->hasData('rule_id')) {
            $this->salesRule->persist();
        }
        $this->data = $this->salesRule->getName();
    }

    /**
     * Persist sales rule.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string|null $key
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Get sales rule.
     *
     * @return SalesRuleInjectable
     */
    public function getSalesRule()
    {
        return $this->salesRule;
    }
}
